<?php

class ViewHelpers {

  /* Inclui arquivos css
   * Se o caminho começar com / deve ser considerado a partir da pasta ASSETS_FOLDER
   * caso contrário a partir de ASSETS_FORLDER/css/
   */
  public static function stylesheetIncludeTag() {
    $params = func_get_args();

    $links = "";
    foreach($params as $param) {
      $path = ASSETS_FOLDER;
      $path .= (substr($param, 0, 1) === '/') ? $param : '/css/' . $param ;
      $links .= "<link href='{$path}' rel='stylesheet' type='text/css' />";
    }
    return $links;
  }

  /*
   * Inclui arquivos js
   * Se o caminho começar com / deve ser considerado a partir da pasta ASSETS_FOLDER
   * caso contrário a partir de ASSETS_FORLDER/css/
   */
  public static function javascriptIncludeTag(){
    $params = func_get_args();

    $links = "";
    foreach($params as $param){
      $path = ASSETS_FOLDER;
      $path .= (substr($param, 0, 1) === '/') ? $param : '/js/' . $param ;
      $links .= "<script src='{$path}' type='text/JavaScript'></script>";
    }
    return $links;
  }

  /*
   * Função para criar links.
   * Importante para definir os caminhos dos arquivos
   * Caso começe com / indica caminho absolute a partir do root da aplicação,
   * caso contrário é camaminho relativo
   */
  public static function linkTo($path, $name, $options = '') {
    if (substr($path, 0, 1) == '/')
      $link = SITE_ROOT . $path;
    else
      $link = $path;
    return "<a href='{$link}' {$options}> $name </a>";
  }

  /*
   * Função para criação de urls
   * Importante, pois com elas não é necessário fazer diversas
   * alterações quando mudar a url principal do site
   */
  public static function urlFor($path){
    return SITE_ROOT . $path;
  }

  public static function imageTag($path, $options = "") {
    $full_path = ASSETS_FOLDER;
    $full_path .= (substr($path, 0, 1) === '/') ? $path : '/images/' . $path ;
    return "<img src=\"{$full_path}\" {$options} />";
  }

  /*
   * Método destinada ao redirecionamento de páginas
   * Lembre-se que quando um endereço inicia-se com '/' diz respeito
   * a um caminho absoluto, caso contrário é um caminho relativo.
   */
  public static function redirectTo($address) {
    if (substr($address, 0, 1) == '/')
      header('location: ' . SITE_ROOT . $address);
    else
      header('location: ' . $address);
    exit();
  }

  public static function previousUrlOr($address = '/' ) {
    if (isset($_SERVER['HTTP_REFERER'])){
      return 'javascript:history.back()';
      //return $_SERVER['HTTP_REFERER'];
    }else{
      return ViewHelpers::urlFor($address);
    }
  }

  /*
   * Função para converter boleano em formato amigável
   */
  public static function prettyBool($value){
    return $value ? 'Sim' : 'Não';
  }

  public static function dateFormat($date){
    return date('d/m/Y', strtotime($date));
  }

  public static function dateTimeFormat($dateTime){
    return date('d/m/Y H:i:s', strtotime($dateTime));
  }

  public static function currencyFormat($number) {
     return 'R$ ' . number_format($number, 2, ',', '.');
  }

  public static function activeClass($route) {
    $route = SITE_ROOT . $route;
    if (preg_match('#^' . $route . '$#', $_SERVER['REQUEST_URI']))
      return 'active';

    return '';
  }

  public static function fullTitle($pageTitle = "") {
    $baseTitle = APP_NAME;
    if (empty($pageTitle))
      return $baseTitle;
    else
      return $pageTitle . " | " . $baseTitle;
  }

  public static function truncate($text, $chars = 25) {
    if(strlen($text) < $chars) return $text;

    $text = $text." ";
    $text = substr($text,0,$chars);
    $text = substr($text,0,strrpos($text,' '));
    $text = $text."...";
    return $text;
  }

  public static function paginate($pageController) {
    $paginationHTML = "<nav><ul class='pagination'>";

    if($pageController->page > 1) {
      $paginationHTML .= "<li><a href=";
      $previous = $pageController->page - 1;
      $paginationHTML .= self::urlFor("/{$pageController->urlSegment}/pagina/{$previous}");
      $paginationHTML .= " aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
    }

    for($i = 1; $i <= $pageController->totalPages; $i++) {
      $class = $i == $pageController->page ? 'active' : '';
      $url = ViewHelpers::urlFor("/{$pageController->urlSegment}/pagina/{$i}");
      $paginationHTML .= "<li class='{$class}'><a class='item' href='{$url}'>{$i}</a></li>";
    }

    if($pageController->page < $pageController->totalPages) {
      $paginationHTML .= "<li><a href=";
      $next = $pageController->page + 1;
      $paginationHTML .= self::urlFor("/{$pageController->urlSegment}/pagina/{$next}");
      $paginationHTML .= " aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li></ul></nav>";
    }

    return $paginationHTML;
  }
}
?>
