<?php class LegalPerson extends Client {

  private $cnpj;
  private $legalName;
  private $tradeName;

  public function setCNPJ($cnpj) {
    $this->cnpj = $cnpj;
  }

  public function setDoc($cnpj) {
    $this->setCNPJ($cnpj);
  }

  public function getDoc() {
    return $this->cnpj;
  }

  public function setLegalName($legalName) {
    $this->legalName = $legalName;
  }

  public function setMoreNames($name) {
    $this->setLegalName($name);
  }

  public function getMoreNames() {
    return $this->legalName;
  }

  public function setTradeName($tradeName) {
    $this->tradeName = $tradeName;
  }

  public function setName($name) {
    $this->setTradeName($name);
  }

  public function getName() {
    return $this->tradeName;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function maskDoc() {
    return preg_replace('/^(\d{2})(\d{3}){1}(\d{3}){1}(\d{4}){1}(\d{2})$/', '${1}.${2}.${3}/${4}-${5}', $this->cnpj);
  }

  private static function createFullProduct($row) {
    $product = new Product();
    $product->setId($row['id']);
    $product->setName($row['name']);
    $product->setCategoryId($row['category_id']);
    $product->setPrice($row['price']);
    $product->setCreatedAt($row['created_at']);

    $category = new Category();
    $category->setId($row['category_id']);
    $category->setName($row['category_name']);
    $category->setCreatedAt($row['category_created_at']);

    $product->setCategory($category);

    // Debug::log($product);

    return $product;
  }

  public function validates() {
    Validations::notEmpty($this->cnpj, 'doc', $this->errors);
    Validations::notEmpty($this->tradeName, 'name', $this->errors);
    Validations::notEmpty($this->legalName, 'more_names', $this->errors);
    Validations::notEmpty($this->zipCode, 'zip_code', $this->errors);
    Validations::notEmpty($this->street, 'street', $this->errors);
    Validations::notEmpty($this->num, 'num', $this->errors);
    Validations::notEmpty($this->cityId, 'city_id', $this->errors);
    //Validations::uniqueField($this->cnpj, 'cnpj', 'natural_persons', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO clients (zip_code, type, city_id)
            VALUES (:zip_code, :type, :city_id);

            INSERT INTO
              legal_persons (id, cnpj, trade_name, legal_name)
            VALUES
              ((SELECT LAST_INSERT_ID()), :cnpj, :name, :more_names);";

    $params = array('zip_code' => $this->zipCode,
                    'type' => $this->type,
                    'city_id' => $this->cityId,
                    'cnpj' => $this->cnpj,
                    'name' => $this->tradeName,
                    'more_names' => $this->legalName);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());

    return true;
  }

  // public static function all() {
  //     $sql = "SELECT
  //         *
  //       FROM
  //         clients
  //       JOIN
  //         legal_persons
  //       ON
  //         clients.id = legal_persons.id";
  //
  //     $db = Database::getConnection();
  //     $statement = $db->prepare($sql);
  //     $resp = $statement->execute();
  //
  //     $clients = [];
  //
  //     if(!$resp) return $clients;
  //
  //     while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
  //       //$clients[] = self::createFullProduct($row);
  //       $clients[] = new LegalPerson($row);
  //     }
  //     return $clients;
  // }

  public static function findById($id) {
    $db = Database::getConnection();
    // $sql = "SELECT * FROM products WHERE id = ?";
    $sql = "SELECT
        products.id, products.name, products.category_id, products.price, products.created_at,
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
        products.id, products.name, products.category_id, products.price, products.created_at,
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
        products.id, products.name, products.category_id, products.price, products.created_at,
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

  public function update($data = array()) {
    if(!$this->hasChange($data)) return true;

    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,
                    'category_id' => $this->categoryId,
                    'id' => $this->id);

    $sql = "UPDATE products SET name=:name, category_id=:category_id WHERE id=:id";

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

}


?>
