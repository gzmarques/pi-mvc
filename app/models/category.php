<?php class Category extends Base {

  private $name;

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function validates() {
    if ($this->newRecord() || $this->changedFieldValue('name', 'categories')) {
      Validations::notEmpty($this->name, 'name', $this->errors);
      Validations::uniqueField($this->name, 'name', 'categories', $this->errors);
    }
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO categories (name)
            VALUES (:name);";

    $params = array('name' => $this->name);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());

    return true;
  }

  public static function all() {
      $sql = "SELECT * FROM categories";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute();

      $categories = [];

      if(!$resp) return $categories;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          $categories[] = new Category($row);
      }
      return $categories;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM categories WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $category = new Category($row);
      return $category;
    }

    return null;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,
      'id' => $this->id);

    $sql = "UPDATE categories SET name=:name WHERE id=:id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function delete() {
    $db = Database::getConnection();

    $params = array('id' => $this->id);

    $sql = "DELETE FROM categories WHERE id=:id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

}


?>
