<?php class Report extends Base {

  public static function bestSellers($options = []) {
    $sql = "SELECT
              products.id, products.name, products.category_id, products.price, products.created_at, products.stock, products.total_sold, products.total_pending,
              categories.name AS category_name, categories.created_at AS category_created_at
            FROM
              products
            JOIN
              categories
            ON
              products.category_id = categories.id
            JOIN
              item_order_products
            ON
              item_order_products.product_id = products.id
            JOIN
              orders
            ON
              item_order_products.order_id = orders.id ";

    if($options != null) {
      $sql .= "WHERE DATE(orders.closed_at)";
      if($options['beginDate'] == $options['endDate']) {
        $sql .= " = :beginDate AND DATE(orders.created_at) = :endDate ";
      } else {
        $sql .= " BETWEEN :beginDate AND :endDate ";
      }
    }

    $sql .= "GROUP BY
              products.id
            ORDER BY
              products.total_sold DESC, products.total_pending DESC
            LIMIT 5";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($options);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = Product::createFullProduct($row);
    }
    return $products;

  }

  public static function worstSellers($options = []) {
    $sql = "SELECT
              products.id, products.name, products.category_id, products.price, products.created_at, products.stock, products.total_sold, products.total_pending,
              categories.name AS category_name, categories.created_at AS category_created_at
            FROM
              products
            JOIN
              categories
            ON
              products.category_id = categories.id ";

    if($options != null) {
      $sql .= "WHERE DATE(orders.created_at)";
      if($options['beginDate'] == $options['endDate']) {
        $sql .= " = :beginDate AND DATE(orders.created_at) = :endDate ";
      } else {
        $sql .= " BETWEEN :beginDate AND :endDate ";
      }
    }
    $sql .= "GROUP BY
              products.id
            HAVING
              products.total_sold > 0
            ORDER BY
              products.total_sold ASC, products.total_pending ASC
            LIMIT 5";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($options);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = Product::createFullProduct($row);
    }
    return $products;

  }

  public function bestEmployee($options = []) {
    $sql = "SELECT users.first_name, users.last_name, users.total_sold, users.total_pending
            FROM users
            JOIN orders
            ON orders.user_id = users.id ";

    if($options != null) {
      $sql .= "WHERE DATE(orders.created_at)";
      if($options['beginDate'] == $options['endDate']) {
        $sql .= " = :beginDate AND DATE(orders.created_at) = :endDate ";
      } else {
        $sql .= " BETWEEN :beginDate AND :endDate ";
      }
    }

    $sql .= "GROUP BY user_id ORDER BY users.total_sold DESC, users.total_pending DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($options);

    $users = [];

    if(!$resp) return $users;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $users[] = new User($row);
    }
    return $users;
  }

}
