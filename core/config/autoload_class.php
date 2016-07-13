<?php
  require_once 'core/lib/active_support.php';
  class AutoloadClass {
    private $paths;

    public function __construct() {
        spl_autoload_register(array($this, 'load'));
    }

    public function getPath($classFileName) {
      foreach ($this->paths as $path)
        if(file_exists($path . $classFileName))
          return $path . $classFileName;

      return false;
    }

    public function addPath($path) {
      $this->paths[] = APP_ROOT_FOLDER . $path;
    }

    private function load($className) {
      $classFileName = $this->setFileName($className);

      if ($this->getPath($classFileName))
        require_once $this->getPath($classFileName);

      return false;
    }

    private function setFileName($className) {
      $className = ActiveSupport::camelToSnake($className);
      $className = ActiveSupport::namespaceToPath($className);

      return $className . '.php';
    }

  }
?>
