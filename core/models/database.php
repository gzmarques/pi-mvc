<?php
class Database {
  public static $instance;

  private function __construct() { }

  public static function getConnection() {

    if (!isset(self::$instance)) {
      require_once 'config/database_config.php';
      $db_ci = "mysql:host=$db_host;port=$db_port;dbname=$db_dbname;";
      $encode = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

      self::$instance = new PDO($db_ci, $db_user, $db_pswd, $encode);
      Logger::getInstance()->log("Aberta conexão com banco de dados!", Logger::NOTICE);

      /* Throw exceptions */
      self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      /* Conversion of NULL and empty strings */
      //self::$instance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING); 
    }
    return self::$instance;
  }
} ?>
