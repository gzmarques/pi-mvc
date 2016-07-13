<?php
abstract class Base {
    protected $id;
    protected $createdAt;
    protected $errors = array();

    public function __construct($data = array()){
      $this->setData($data);
    }

    public function validates(){}

    public function getId() {
      return $this->id;
    }

    public function setId($id) {
      $this->id = $id;
    }

    public function getCreatedAt(){
      return $this->createdAt;
    }

    public function setCreatedAt($createdAt){
      $this->createdAt = $createdAt;
    }

    public function getErrors($index = null) {
      if ($index == null)
        return $this->errors;

      if (isset($this->errors[$index]))
        return $this->errors[$index];

      return false;
    }

    public function isValid() {
      $this->errors = array();
      $this->validates();
      return empty($this->errors);
    }

    public function newRecord(){
      return empty($this->id);
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

    public function setData($data = array()) {
      // Debug::log($data);exit;
      foreach($data as $key => $value){
         $method = "set{$key}";
         $method = ActiveSupport::snakeToCamelCase($method);
         $this->$method(strip_tags(trim($value)));
      }

    }

    public function hasChange($data = array()) {
      foreach($data as $key => $value){
         $method = "get{$key}";
         $method = ActiveSupport::snakeToCamelCase($method);

         if($this->$method() != $value)
          return true;
      }
      return false;
    }

    public static function whereCityAsLikeJson($partial) {
      $db = Database::getConnection();
      $sql = "SELECT
          cities.id AS city_id, cities.name AS city_name, states.id AS state_id, states.name AS state_name, states.code AS state_code, countries.id AS country_id, countries.name AS country_name
        FROM
          cities
        JOIN
          states
        ON
          cities.state_id = states.id
        JOIN
          countries
        ON
          states.country_id = countries.id
        WHERE
          cities.name LIKE :partial";

      $params = array('partial' => $partial . "%");

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $products = [];

      if(!$resp) return $cities;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          $cities[] = array('value' => $row['city_name'] . "/" . $row['state_code'],
                              'data' => $row['city_id'],
                              'state_code' => $row['state_code'],
                              'state' => $row['state_name'],
                              'state_id' => $row['state_id'],
                              'country' => $row['country_name'],
                              'contry_id' => $row['country_id']);
      }
      $suggestions = array('suggestions' => $cities);
      return json_encode($suggestions);
    }
} ?>
