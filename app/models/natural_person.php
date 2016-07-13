<?php class NaturalPerson extends Client {

  private $cpf;
  private $firstName;
  private $lastName;
  private $birthDate;

  public function setCPF($cpf) {
    $this->cpf = $cpf;
  }

  public function setDoc($cpf) {
    $this->setCPF($cpf);
  }

  public function getDoc() {
    return $this->cpf;
  }

  public function setName($firstName) {
    $this->setFirstName($firstName);
  }

  public function setFirstName($firstName) {
    $this->firstName = $firstName;
  }

  public function getName() {
    return $this->firstName;
  }

  public function setMoreNames($lastName) {
    $this->setLastName($lastName);
  }

  public function setLastName($lastName) {
    $this->lastName = $lastName;
  }

  public function getMoreNames() {
    return $this->lastName;
  }

  public function setBirthDate($birthDate) {
    $this->birthDate = $birthDate;
  }

  public function getBirthDate() {
    return $this->birthDate;
  }

  public function maskDoc() {
    return preg_replace('/^(\d{1,3})(\d{3}){1}(\d{3}){1}(\d{2})$/', '${1}.${2}.${3}-${4}', $this->cpf);
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
    Validations::notEmpty($this->cpf, 'doc', $this->errors);
    Validations::notEmpty($this->firstName, 'name', $this->errors);
    Validations::notEmpty($this->lastName, 'more_names', $this->errors);
    Validations::notEmpty($this->zipCode, 'zip_code', $this->errors);
    Validations::notEmpty($this->street, 'street', $this->errors);
    Validations::notEmpty($this->num, 'num', $this->errors);
    Validations::notEmpty($this->cityId, 'city_id', $this->errors);
    Validations::uniqueField($this->cpf, 'cpf', 'natural_persons', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;
    $sql = "INSERT INTO clients (zip_code, type, city_id)
            VALUES (:zip_code, :type, :city_id);

            INSERT INTO
              natural_persons (id, cpf, first_name, last_name)
            VALUES
              ((SELECT LAST_INSERT_ID()), :cpf, :first_name, :last_name);";

    $params = array('zip_code' => $this->zipCode,
                    'type' => $this->type,
                    'city_id' => $this->cityId,
                    'cpf' => $this->cpf,
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName);

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
  //         natural_persons
  //       ON
  //         clients.id = natural_persons.id";
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
  //       $clients[] = new NaturalPerson($row);
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
