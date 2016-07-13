<?php abstract class Client extends Base {

  protected $type;

  protected $zipCode;
  protected $street;
  protected $num;
  protected $cityId;
  protected $city;
  protected $phone;
  protected $mobile;
  protected $leaveMessage;

  public function setZipCode($zipCode) {
    $this->zipCode = $zipCode;
  }

  public function getZipCode() {
    return $this->zipCode;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function setCityId($cityId) {
    $this->cityId = $cityId;
  }

  public function getCityId() {
    return $this->cityId;
  }

  public function setCity($city) {
    $this->city = $city;
  }

  public function getCity() {
    return $this->city;
  }

  public function setNum($num) {
    $this->num = $num;
  }

  public function getNum() {
    return $this->num;
  }

  public function setStreet($street) {
    $this->street = $street;
  }

  public function getStreet() {
    return $this->street;
  }

  public function setPhone($phone) {
    $this->phone = $phone;
  }

  public function getPhone() {
    return $this->phone;
  }
  public function setMobile($mobile) {
    $this->mobile = $mobile;
  }

  public function getMobile() {
    return $this->mobile;
  }

  public function setLeaveMessage($leaveMessage) {
    $this->leaveMessage = $leaveMessage;
  }

  public function getLeaveMessage() {
    return $this->leaveMessage;
  }

  public function getName(){}

  public function getDoc(){}

  public function maskDoc(){}

  public function getMoreNames(){}

  private static function createFullClient($row) {
    if($row['type'] == 0) {
      $client = new NaturalPerson();
      $client->setId($row['id']);
      $client->setFirstName($row['first_name']);
      $client->setLastName($row['last_name']);
      $client->setCPF($row['cpf']);
      $client->setZipCode($row['zip_code']);
      $client->setType($row['type']);
    } else {
      $client = new LegalPerson();
      $client->setId($row['id']);
      $client->setTradeName($row['trade_name']);
      $client->setCNPJ($row['cnpj']);
      $client->setZipCode($row['zip_code']);
      $client->setType($row['type']);
    }
    return $client;
  }

  public function validates() {
    Validations::notEmpty($this->zipCode, 'zip_code', $this->errors);
    Validations::notEmpty($this->street, 'street', $this->errors);
    Validations::notEmpty($this->num, 'num', $this->errors);
    Validations::notEmpty($this->city_id, 'city_id', $this->errors);
  }

  // public function save() {
  //   //if (!$this->isvalid()) return false;
  //
  //   $sql = "INSERT INTO clients (zip_code, type, city_id)
  //           VALUES (:zip_code, :type, :city_id);";
  //
  //   $params = array('zip_code' => $this->zipCode,
  //                   'type' => $this->type,
  //                   'city_id' => $this->cityId);
  //
  //   $db = Database::getConnection();
  //   $statement = $db->prepare($sql);
  //   $resp = $statement->execute($params);
  //
  //   $this->setId($db->lastInsertId());
  //   $this->setCreatedAt(date());
  //
  //   return true;
  // }

  public static function all($options = []) {
      $sql = "SELECT
                clients.type, clients.id, clients.zip_code,
                natural_persons.first_name, natural_persons.last_name, natural_persons.cpf,
                legal_persons.trade_name, legal_persons.cnpj,
                cities.id AS city_id, cities.name AS city_name
              FROM
                clients
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
                cities.id = clients.city_id";
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

      $clients = [];

      if(!$resp) return $clients;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = self::createFullClient($row);
        //$clients['naturals'][] = self::createFullClient($row);
        //$clients[] = self::createFullProduct($row);
        // if($row['type'] == 1)
        //   $clients['naturals'][] = new NaturalPerson($row);
        // else
        //   $clients['legals'][] = new LegalPerson($row);
      }
      return $clients;
  }

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

    $sql = "SELECT
              clients.type, clients.id AS client_id,
              natural_persons.first_name AS first_name,
              legal_persons.trade_name AS trade_name,
              cities.id AS city_id, cities.name AS city_name
            FROM
              clients
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
              natural_persons.first_name LIKE :partial
            OR
              legal_persons.trade_name LIKE :partial";

    $params = array('partial' => "%" . $partial . "%");

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $clients = [];

    if(!$resp) return $clients;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = self::createFullProduct($row);
    }
    return $clients;
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

  public static function prettyType($value){
    return $value ? 'Pessoa Jurídica' : 'Pessoa Física';
  }

  public static function whereNameAsLikeJson($partial) {
    $db = Database::getConnection();
    $sql = "SELECT
              clients.type, clients.id AS client_id,
              COALESCE(natural_persons.first_name, legal_persons.trade_name) AS name,
              COALESCE(natural_persons.last_name, legal_persons.legal_name) AS more_names,
              cities.id AS city_id, cities.name AS city_name
            FROM
              clients
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
              natural_persons.first_name LIKE :partial
            OR
              legal_persons.trade_name LIKE :partial
            OR
              natural_persons.last_name LIKE :partial
            OR
              legal_persons.legal_name LIKE :partial";

    $params = array('partial' => "%" . $partial . "%");

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $clients = [];

    if(!$resp) return $clients[] = array('value' => 'Nenhum cliente encontrado');;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = array('value' => $row['name'] . " " . $row['more_names'], 'data' => $row['client_id']);
    }
    $suggestions = array('suggestions' => $clients);
    return json_encode($suggestions);
  }

  public static function count() {
    $sql = "SELECT COUNT(*) FROM clients";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    return $statement->fetch()[0];
  }

}

?>
