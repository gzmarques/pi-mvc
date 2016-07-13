<?php class Router {

  private $url;
  private $postRoutes = array();
  private $getRoutes = array();

  public function __construct($url) {
    Logger::getInstance()->log($url, Logger::NOTICE);
    $this->url = str_replace(SITE_ROOT, '', $url);
    $this->url = preg_replace('/\?.*/', '', $this->url); // Remove get params from URL
  }

  public function get($route, $options){
    $this->getRoutes[$route] = $options;
  }

  public function post($route, $options){
    $this->postRoutes[$route] = $options;
  }

  public function load() {
     if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $this->find($this->postRoutes);
     } else
        $this->find($this->getRoutes);
  }

  private function find($routes) {
      foreach($routes as $route => $options){

        $params = array();
            // Debug::log($this->isRightRoute($route, $params), $route);

        if ($this->isRightRoute($route, $params)) {
           Logger::getInstance()->log("match: {$route} with {$this->url}", Logger::NOTICE);

           $controller_name = $options['controller'];
           $action_name = $options['action'];

           $merged_params = array_merge($this->params(), $params);

           $controller = new $controller_name();
           $controller->setParams($merged_params);
           $controller->beforeAction($action_name);

           $controller->setView(ActiveSupport::camelToSnake($action_name));
           $controller->setControllerName($controller_name);
           $controller->$action_name();
           $controller->render();

           return;
        }
      }
     $this->pageNotFound();
  }

  private function isRightRoute($route, &$params) {
    //Debug::log($route, $params, $this->url);
    $route = explode('/', $route);
    $url = explode('/', $this->url);

    if (sizeof($route) != sizeof($url)) return false;

    for ($i = 0; $i < sizeof($route); $i++) {

      if (preg_match('/^:[a-z,_]+$/', $route[$i])) {

          $params[$route[$i]] = $url[$i];
          // Debug::log($params, $route);
          continue;
      }else if ($route[$i] !== $url[$i]) {
        return false;
      }
    }

    return true;
  }

  private function params() {
     if ($_SERVER['REQUEST_METHOD'] === 'POST')
       return $_POST;
     else
       return $_GET;
  }

  private function pageNotFound() {
     Logger::getInstance()->log("URL NOT FOUND: " . $_SERVER['REQUEST_URI'], Logger::ERROR);
     Flash::message('danger', 'Página não encontrada!');
     header('location: ' . SITE_ROOT . '/');
     exit();
  }
}?>
