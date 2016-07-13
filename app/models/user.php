<?php class User extends Base {

  private $firstName;
  private $lastName;
  private $cpf;
  private $birthDate;
  private $admissionDate;
  private $firingDate;
  private $email;
  private $password;
  private $totalSold;
  private $totalPending;
  private $permissionClass;

  public function setFirstName($firstName) {
    $this->firstName = $firstName;
  }
  public function getFirstName() {
    return $this->firstName;
  }

  public function setLastName($lastName) {
    $this->lastName = $lastName;
  }
  public function getLastName() {
    return $this->lastName;
  }

  public function setBirthDate($birthDate) {
    $this->birthDate = $birthDate;
  }
  public function getBirthDate() {
    return $this->birthDate;
  }

  public function setCPF($cpf) {
    $this->cpf = $cpf;
  }
  public function getCPF() {
    return $this->cpf;
  }

  public function setAdmissionDate($admissionDate) {
    $this->admissionDate = $admissionDate;
  }
  public function getAdmissionDate() {
    return $this->admissionDate;
  }

  public function setFiringDate($firingDate) {
    $this->firingDate = $firingDate;
  }
  public function getFiringDate() {
    return $this->firingDate;
  }

  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }

  public function setPassword($password) {
    $this->password= $password;
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

  public function setPermissionClass($permissionClass) {
    $this->permissionClass = $permissionClass;
  }
  public function getPermissionClass() {
    return $this->permissionClass;
  }

  public function validates() {
    Validations::notEmpty($this->firstName, 'first_name', $this->errors);
    Validations::notEmpty($this->lastName, 'last_name', $this->errors);

    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('email', 'users')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'users', $this->errors);
    }

    if ($this->newRecord()) /* Caso a senha seja vazia não deve ser atualizada */
      Validations::notEmpty($this->password, 'password', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO users (first_name, last_name, email, password, permission_class)
            VALUES (:first_name, :last_name, :email, :password, :permission);";

    $params = array('first_name' => $this->firstName, 'last_name' => $this->lastName, 'email' => $this->email,
                    'password' => $this->cryptographyPassword($this->password), 'permission' => $this->permissionClass);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    return true;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('first_name' => $this->firstName,
      'last_name' => $this->lastName,
      'email' => $this->email,
      'id' => $this->id);

    if (empty($this->password)) {
      $sql = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email WHERE id = :id";
    } else {
      $params['password'] = $this->cryptographyPassword($this->password);
      $sql = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email, password=:password WHERE id = :id";
    }

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function authenticate($password) {
    if ($this->password === $this->cryptographyPassword($password)) {
      SessionHelpers::logIn($this);
      return true;
    }
    return false;
  }

  private function cryptographyPassword($password) {
    return sha1(sha1('dw3'.$password));
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM users WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $user = new User($row);
      return $user;
    }

    return null;
  }

  public static function findByEmail($email) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM users WHERE email = ?";
    $params = array($email);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $user = new User($row);
      return $user;
    }

    return null;
  }

} ?>
