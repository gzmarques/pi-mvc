<?php class Product extends Base {

  private $name;
  private $category;
  private $categoryId;
  private $price;
  private $stock;
  private $totalSold;
  private $totalPending;

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  public function getCategoryId() {
    return $this->categoryId;
  }

  public function setCategory($category) {
    $this->category = $category;
  }

  public function getCategory() {
    return $this->category;
  }

  public function setPrice($price) {
    $this->price = $price;
  }

  public function getPrice() {
    return $this->price;
  }

  public function setStock($stock) {
    $this->stock = $stock;
  }

  public function getStock() {
    return $this->stock;
  }

  public function setTotalSold($totalSold) {
    $this->totalSold = $totalSold;
  }

  public function getTotalSold() {
    return $this->totalSold;
  }

  public function setTotalPending($totalPending) {
    $this->totalPending = $totalPending;
  }

  public function getTotalPending() {
    return $this->totalPending;
  }

  public function hasSufficient($amount) {
    if ($this->stock < $amount) {
      //if ($errors !== null)
         $this->errors['amount'] = 'Estoque insuficiente!';
      return false;
    }
    return true;
  }

  public static function createFullProduct($row) {
    $product = new Product();
    $product->setId($row['id']);
    $product->setName($row['name']);
    $product->setCategoryId($row['category_id']);
    $product->setPrice($row['price']);
    $product->setCreatedAt($row['created_at']);
    $product->setStock($row['stock']);
    $product->totalSold();
    $product->totalPending();

    $category = new Category();
    $category->setId($row['category_id']);
    $category->setName($row['category_name']);
    $category->setCreatedAt($row['category_created_at']);

    $product->setCategory($category);

    return $product;
  }

  public function validates() {
    if ($this->newRecord() || $this->changedFieldValue('name', 'products')) {
      Validations::uniqueField($this->name, 'name', 'products', $this->errors);
    }
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::notEmpty($this->price, 'price', $this->errors);
    Validations::notEmpty($this->stock, 'stock', $this->errors);
    Validations::notNegative($this->stock, 'stock', $this->errors);
  }

  public function changedFieldValue($field, $table) {
    $db = Database::getConnection();
    $sql = "select {$field} from {$table} where id = :id";

    $statement = $db->prepare($sql);
    $params = array('id' => $this->id);
    $statement->execute($params);
    $result = $statement->fetch();

    $method = 'get' . $field;
    $field_from_db = $result[$field];

    Logger::getInstance()->log("Mudou: {$this->$method()}", Logger::NOTICE);

    return $field_from_db !== $this->$method();
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO products (name, price, category_id, stock)
            VALUES (:name, :price, :category_id, :stock);";

    $params = array('name' => $this->name,
                    'price' => $this->price,
                    'category_id' => $this->categoryId,
                    'stock' => $this->stock);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());

    return true;
  }

  public static function all($options = []) {

      $sql = "SELECT
          products.id, products.name, products.category_id, products.price, products.created_at, products.stock,
          categories.name AS category_name, categories.created_at AS category_created_at
        FROM
          products, categories
        WHERE
          products.category_id = categories.id";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);

      if(sizeof($options) != 0) {
          $sql .= " ORDER BY :orderBy LIMIT :limit OFFSET :offset";
          $statement = $db->prepare($sql);
          $statement->bindParam(":orderBy", $options['orderBy'], PDO::PARAM_INT);
          $statement->bindParam(":limit", $options['limit'], PDO::PARAM_INT);
          $statement->bindParam(":offset", $options['offset'], PDO::PARAM_INT);

      }

      $resp = $statement->execute();

      $products = [];

      if(!$resp) return $products;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $products[] = self::createFullProduct($row);
      }
      return $products;
  }

  public static function findById($id) {

    $sql = "SELECT
        products.id, products.name, products.category_id, products.price, products.created_at, products.stock,
        categories.name AS category_name, categories.created_at AS category_created_at
      FROM
        products, categories
      WHERE
        products.id = ?";

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createFullProduct($row);
    }
    return null;
  }

  public static function findByCategory($categoryId) {
    $db = Database::getConnection();
    // $sql = "SELECT * FROM products WHERE category_id = :category_id;";
    $sql = "SELECT
        products.id, products.name, products.category_id, products.price, products.created_at, products.stock,
        categories.name AS category_name, categories.created_at AS category_created_at
      FROM
        products
      JOIN
        categories
      ON
        products.category_id = categories.id
      WHERE
        products.category_id = :category_id";

    $params = array('category_id' => $categoryId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $products[] = self::createFullProduct($row);
    }
    return $products;
  }

  public static function findByName($partial) {
    $db = Database::getConnection();
    // $sql = "SELECT * FROM products WHERE name LIKE :partial;";
    $sql = "SELECT
        products.id, products.name, products.category_id, products.price, products.created_at, products.stock,
        categories.name AS category_name, categories.created_at AS category_created_at
      FROM
        products, categories
      WHERE
        products.name LIKE :partial AND products.category_id = categories.id";

    $params = array('partial' => "%" . $partial . "%");

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $products[] = self::createFullProduct($row);
    }
    return $products;
  }

  public static function findByAny($name) {
    $db = Database::getConnection();
    $sql = "SELECT
        products.id, products.name, products.category_id, products.price, products.created_at, products.stock,
        categories.name AS category_name, categories.created_at AS category_created_at
      FROM
        products
      JOIN
        categories
      ON
        products.category_id = categories.id
      WHERE
        products.name LIKE :name
      OR
        products.id = :id";

    $params = array('name' => "%" . $name . "%",
                    'id' => $name);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $products[] = self::createFullProduct($row);
    }
    return $products;
  }

  public function update($data = array()) {
    if(!$this->hasChange($data)) return true;

    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,
                    'category_id' => $this->categoryId,
                    'id' => $this->id,
                    'stock' => $this->stock);

    $sql = "UPDATE products SET name=:name, category_id=:category_id, stock=:stock WHERE id=:id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function delete() {
    $db = Database::getConnection();

    $params = array('id' => $this->id);

    $sql = "DELETE FROM products WHERE id=:id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function whereNameAsLikeJson($partial) {
    $db = Database::getConnection();
    $sql = "SELECT
        products.id, products.name, products.stock, products.price
      FROM
        products
      WHERE
        products.name LIKE :partial";

    $params = array('partial' => "%" . $partial . "%");

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $products[] = array('value' => $row['name'],
                            'data' => $row['id'],
                            'stock' => $row['stock'],
                            'price' => $row['price']);
    }
    $suggestions = array('suggestions' => $products);
    return json_encode($suggestions);
  }

  public static function count() {
    $sql = "SELECT COUNT(*) FROM products";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    return $statement->fetch()[0];
  }

  public function totalSold() {
    $sql = "SELECT
        sum(item_order_products.amount) AS total_sold
      FROM
        item_order_products
      JOIN
        products
      ON
        item_order_products.product_id = products.id
      JOIN
        orders
      ON
        item_order_products.order_id = orders.id
      WHERE
        orders.status = 1
      GROUP BY
        products.id
      HAVING
        products.id = {$this->id}";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute();

      $result = $statement->fetch(PDO::FETCH_ASSOC);

      if($result)
        $this->setTotalSold($result['total_sold']);
      else
        $this->setTotalSold(0);

      // $sqlUpdate = "UPDATE products SET total_sold={$this->totalSold} WHERE id={$this->id} ";
      // $statement = $db->prepare($sqlUpdate);
      // $resp = $statement->execute();


  }

  public function totalPending() {
    $sql = "SELECT
        sum(item_order_products.amount) AS total_pending
      FROM
        item_order_products
      JOIN
        products
      ON
        item_order_products.product_id = products.id
      JOIN
        orders
      ON
        item_order_products.order_id = orders.id
      WHERE
        orders.status = 0
      GROUP BY
        products.id
      HAVING
        products.id = {$this->id}";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute();

      $result = $statement->fetch(PDO::FETCH_ASSOC);

      if($result)
        $this->setTotalPending($result['total_pending']);
      else
        $this->setTotalPending(0);

      // $sqlUpdate = "UPDATE products SET total_sold={$this->totalSold} WHERE id={$this->id} ";
      // $statement = $db->prepare($sqlUpdate);
      // $resp = $statement->execute();


  }

}


?>
