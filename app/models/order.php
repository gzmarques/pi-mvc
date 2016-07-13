<?php class Order extends Base {

  private $clientId;
  private $userId;
  private $total;
  private $status;
  private $client;
  private $user;
  private $items;
  private $closedAt;

/** Setters and Getters **/
  public function setClientId($clientId) {
    $this->clientId = $clientId;
  }

  public function getClientId() {
    return $this->clientId;
  }

  public function setUserId($userId) {
    $this->userId = $userId;
  }

  public function getUserId() {
    return $this->userId;
  }

  public function setTotal($total) {
    $this->total = $total;
  }

  public function getTotal() {
    return $this->total;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function setClient($client) {
    $this->client = $client;
  }

  public function getClient() {
    return $this->client;
  }

  public function setUser($user) {
    $this->user = $user;
  }

  public function getUser() {
    return $this->user;
  }

  public function setItems($items) {
    $this->items = $items;
  }

  public function getItems() {
    return $this->items;
  }

  public function setClosedAt($closedAt) {
    $this->closedAt = $closedAt;
  }

  public function getClosedAt() {
    return $this->closedAt;
  }

  public function isAllowedUser() {
    return SessionHelpers::currentUser()->getId() == $this->user->getId() || SessionHelpers::currentUser()->getPermissionClass() == 9;
  }

  private static function createFullOrder($row) {
    $order = new Order();
    $order->setId($row['id']);
    $order->setTotal($row['total']);
    $order->setCreatedAt($row['created_at']);
    $order->setStatus($row['status']);
    $order->setClientId($row['client_id']);
    $order->setUserId($row['user_id']);

    $user = new User();
    $user->setId($row['user_id']);
    $user->setFirstName($row['user_name']);

    if($row['type'] == 0) {
      $client = new NaturalPerson();
      $client->setId($row['client_id']);
      $client->setFirstName($row['first_name']);
    } else {
      $client = new LegalPerson();
      $client->setId($row['client_id']);
      $client->setTradeName($row['trade_name']);
    }
    $client->setType($row['type']);

    $order->setUser($user);
    $order->setClient($client);
    $order->setItems($order->getProducts());

    return $order;
  }

  public function validates() {
    if($this->newRecord()) {
      Validations::notEmpty($this->clientId, 'client_id', $this->errors);
      Validations::notEmpty($this->userId, 'user_id', $this->errors);
    }
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO orders (client_id, user_id)
            VALUES (:client_id, :user_id);";

    $params = array('client_id' => $this->clientId,
                    'user_id' => $this->userId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());

    return true;
  }

  public function delete() {
    if($this->status == 1) return false;
    $db = Database::getConnection();

    $params = array('id' => $this->id);

    foreach ($this->items as $item) {
      $this->removeProduct($item);
    }

    $sql = "DELETE FROM orders WHERE id=:id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function all($userId, $options = []) {
    $sql = "SELECT
              orders.id, orders.total, orders.created_at, orders.status, orders.client_id, orders.user_id AS order_user_id,
              clients.type, clients.id AS client_id,
              natural_persons.first_name AS first_name,
              legal_persons.trade_name AS trade_name,
              users.id AS user_id, users.first_name AS user_name, users.email,
              cities.id AS city_id, cities.name AS city_name
            FROM
              clients
            JOIN
              orders
            ON
              clients.id = orders.client_id
            JOIN
              users
            ON
              orders.user_id = users.id
            LEFT JOIN
              natural_persons
            ON
              clients.id = natural_persons.id
            LEFT JOIN
              legal_persons
            ON
              clients.id = legal_persons.id
            LEFT JOIN
              cities
            ON
              cities.id = clients.city_id
            ORDER BY
              users.id <> {$userId}, users.id, orders.id";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);

      if(sizeof($options) != 0) {
          $sql .= " LIMIT :limit OFFSET :offset";
          $statement = $db->prepare($sql);
          $statement->bindParam(":limit", $options['limit'], PDO::PARAM_INT);
          $statement->bindParam(":offset", $options['offset'], PDO::PARAM_INT);

      }

      $resp = $statement->execute();

      $orders = [];

      if(!$resp) return $orders;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $orders[] = self::createFullOrder($row);
      }
      return $orders;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT
              orders.id, orders.total, orders.created_at, orders.status, orders.client_id, orders.user_id AS order_user_id,
              clients.type, clients.id AS client_id,
              natural_persons.first_name AS first_name,
              legal_persons.trade_name AS trade_name,
              users.id AS user_id, users.first_name AS user_name, users.email,
              cities.id AS city_id, cities.name AS city_name
            FROM
              clients
            JOIN
              orders
            ON
              clients.id = orders.client_id
            JOIN
              users
            ON
              orders.user_id = users.id
            LEFT JOIN
              natural_persons
            ON
              clients.id = natural_persons.id
            LEFT JOIN
              legal_persons
            ON
              clients.id = legal_persons.id
            LEFT JOIN
              cities
            ON
              cities.id = clients.city_id
            WHERE
              orders.id = ?";

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createFullOrder($row);
    }
    return null;
  }

  public static function findByAny($name) {
    $db = Database::getConnection();
    $sql = "SELECT
              orders.id, orders.total, orders.created_at, orders.status, orders.client_id, orders.user_id AS order_user_id,
              clients.type, clients.id AS client_id,
              natural_persons.first_name AS first_name,
              legal_persons.trade_name AS trade_name,
              users.id AS user_id, users.first_name AS user_name, users.email,
              cities.id AS city_id, cities.name AS city_name
            FROM
              clients
            JOIN
              orders
            ON
              clients.id = orders.client_id
            JOIN
              item_order_products
            ON
              item_order_products.order_id = orders.id
            JOIN
              products
            ON
              products.id = item_order_products.product_id
            JOIN
              users
            ON
              orders.user_id = users.id
            LEFT JOIN
              natural_persons
            ON
              clients.id = natural_persons.id
            LEFT JOIN
              legal_persons
            ON
              clients.id = legal_persons.id
            LEFT JOIN
              cities
            ON
              cities.id = clients.city_id
            WHERE
              natural_persons.first_name LIKE :name
            OR
              legal_persons.trade_name LIKE :name
            OR
              users.first_name LIKE :name
            OR
              orders.id = :id
            OR
              products.name LIKE :name";

    $params = array("name" => "%" . $name . "%",
                    "id" => $name);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $orders = [];

    if(!$resp) return $orders;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $orders[] = self::createFullOrder($row);
    }
    return $orders;
  }

  public function addProduct($product, $amount) {
    if($this->status == 1) return false;
    if(!$product->hasSufficient($amount)) return false;
    if($amount < 0 || $amount == NULL) return false;

    foreach ($this->getItems() as $item) {
      if($item->getProduct()->getId() == $product->getId())
        return $this->updateAmount($item, $item->getAmount() + $amount);
    }

    $sql = "INSERT INTO item_order_products (order_id, product_id, item_price, amount)
            VALUES (:order_id, :product_id, :price, :amount);

            UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=:order_id) WHERE id=:order_id;

            UPDATE products SET stock=stock-:amount, total_pending=total_pending+:amount WHERE id=:product_id;

            UPDATE users SET total_pending=(SELECT SUM(item_price * amount) FROM item_order_products WHERE user_id=:user_id)";

    $params = array('order_id' => $this->getId(),
                    'user_id' => $this->getUser()->getId(),
                    'product_id' => $product->getId(),
                    'price' => $product->getPrice(),
                    'amount' => $amount);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    return $statement->execute($params);

  }

  public function getProducts() {
    $sql = "SELECT
        products.id, products.name, products.category_id, products.price, products.created_at, products.stock,
        categories.name AS category_name, categories.created_at AS category_created_at,
        item_order_products.id AS item_id, item_order_products.item_price, item_order_products.amount, item_order_products.created_at AS item_created_at
      FROM
        products, categories, item_order_products
      WHERE
        products.category_id = categories.id
      AND
        products.id = item_order_products.product_id
      AND
        item_order_products.order_id = ?";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute(array($this->getId()));

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = ItemOrderProduct::createFullItem($row);
    }
    return $products;
  }

  public function removeProduct($item) {
    if($this->status == 1) return false;
    $sql = "DELETE FROM item_order_products WHERE id = :item_id;

            UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=:order_id) WHERE id=:order_id;

            UPDATE products SET stock=stock+:amount, total_sold=total_sold-:amount WHERE id=:product_id";

    $params = array('order_id' => $this->getId(),
                    'item_id' => $item->getId(),
                    'product_id' => $item->getProduct()->getId(),
                    'amount' => $item->getAmount());

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    return $statement->execute($params);

  }

  public function closeOrder() {
    //if(!$this->hasChange($data)) return true;

    //$this->setData($data);
    //if (!$this->isvalid()) return false;
    if($this->status == 1) return true;

    $db = Database::getConnection();
    $params = array('id' => $this->id,
                    'userTotalSold' => $this->total,
                    'user_id' => $this->userId);

    $sql = "UPDATE orders SET status = 1, closed_at = CURRENT_TIMESTAMP WHERE id = :id;
            UPDATE users SET total_sold = total_sold+:userTotalSold, total_pending=total_pending-:userTotalSold WHERE id = :user_id;";

    foreach ($this->items as $item) {
      $item->closeSellSQL($sql, $params);
    }

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function increaseAmount($item, $amount) {
    if($this->status == 1) return false;
    if(!$item->getProduct()->hasSufficient(1)) return false;

    $sql = "UPDATE item_order_products SET amount=amount+1 WHERE id=:item_id;

            UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=:order_id) WHERE id=:order_id;

            UPDATE products SET stock=stock-1, total_pending=total_pending+1 WHERE id=:product_id";

    $params = array('order_id' => $this->getId(),
                    'item_id' => $item->getId(),
                    'product_id' => $item->getProduct()->getId());

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    return $statement->execute($params);
  }

  public function decreaseAmount($item, $amount) {
    if($this->status == 1) return false;
    // Debug::log($this, $amount, $item, $this->amount);exit;

    // if(!$item->getProduct()->hasSufficient(1)) return false;

    $sql = "UPDATE item_order_products SET amount=amount-1 WHERE id=:item_id;

            UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=:order_id) WHERE id=:order_id;

            UPDATE products SET stock=stock+1, total_pending=total_pending-1 WHERE id=:product_id";

    $params = array('order_id' => $this->getId(),
                    'item_id' => $item->getId(),
                    'product_id' => $item->getProduct()->getId());

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    return $statement->execute($params);
  }

  public function updateAmount($item, $amount) {
    if($this->status == 1) return false;
    $params = array('order_id' => $this->getId(),
                    'item_id' => $item->getId(),
                    'product_id' => $item->getProduct()->getId());

    if($item->getAmount() <= $amount) {
      $sql = "UPDATE products SET stock=stock-:amount, total_pending=total_pending+:amount WHERE id=:product_id;
              UPDATE item_order_products SET amount=amount+:amount WHERE id=:item_id;";
      if(!$item->getProduct()->hasSufficient($amount - $item->getAmount())) return false;
      $params['amount'] = $amount - $item->getAmount();
    } else {
      $sql = "UPDATE products SET stock=stock+:amount, total_pending=total_pending-:amount WHERE id=:product_id;
              UPDATE item_order_products SET amount=amount-:amount WHERE id=:item_id;";
      $params['amount'] = $item->getAmount() - $amount;
    }

    $sql .= "UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=:order_id) WHERE id=:order_id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    return $statement->execute($params);
  }

  public static function prettyStatus($value){
    return $value ? 'Fechado' : 'Aberto';
  }

}


?>
