<?php class ItemOrderProduct extends Base {

  private $productId;
  private $product;
  private $amount;
  private $itemPrice;

  public function setProductId($productId) {
    $this->productId = $productId;
  }

  public function getProductId() {
    return $this->productId;
  }

  public function setItemPrice($itemPrice) {
    $this->itemPrice = $itemPrice;
  }

  public function getItemPrice() {
    return $this->itemPrice;
  }

  public function setProduct($product) {
    $this->product = $product;
  }

  public function getProduct() {
    return $this->product;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public static function createFullItem($row) {
    $item = new ItemOrderProduct();
    $item->setId($row['item_id']);
    $item->setProductId($row['id']);
    $item->setItemPrice($row['item_price']);
    $item->setAmount($row['amount']);
    $item->setCreatedAt($row['item_created_at']);

    $product = Product::createFullProduct($row);

    $item->setProduct($product);

    return $item;
  }

  public static function findById($id) {
    $db = Database::getConnection();

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
        item_order_products.id = ?";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute(array($id));

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createFullItem($row);
    }
    return null;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
  }

  public function closeSellSQL(&$sql, &$params) {
    $sql .= " UPDATE products
              SET total_sold = total_sold + :productAmount{$this->id},
                  total_pending=total_pending - :productAmount{$this->id}
              WHERE products.id = :product_id{$this->id};";

    $params["product_id{$this->id}"] = $this->getProduct()->getId();
    $params["productAmount{$this->id}"] = $this->amount;
  }

}


?>
