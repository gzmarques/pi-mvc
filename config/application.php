<?php
  define('APP_NAME', 'Desenvolvimento para web 3');
  define('SITE_ROOT', '/utfpr/web-dev3-code/pi-mvc'); # com barras
  define('APP_ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . SITE_ROOT );
  //define('APP_ROOT_FOLDER', 'https://gzmarques.sytes.net' . SITE_ROOT );
  define('LOG_FILE', APP_ROOT_FOLDER .  '/logs/application.log');

  define('ASSETS_FOLDER', SITE_ROOT .  '/app/assets');

  /* Adicionar pastas defaults para inclução de arquivos com as funções require e include */
  set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT_FOLDER );
  set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT_FOLDER  . '/config/');
  set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT_FOLDER  . '/app/');
  set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT_FOLDER  . '/lib/');
  set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT_FOLDER  . '/vendor/');

  session_start();
  date_default_timezone_set('America/Sao_Paulo');

  require_once 'php_logger/logger.class.php';
  Logger::getInstance()->loadFile(LOG_FILE);

  require_once 'core/config/autoload_class.php';
  $autoload = new AutoloadClass();
  $autoload->addPath('/core/routes/');
  $autoload->addPath('/core/controllers/');
  $autoload->addPath('/core/models/');
  $autoload->addPath('/core/views/');
  $autoload->addPath('/core/lib/');

  $autoload->addPath('/app/controllers/');
  $autoload->addPath('/app/models/');
  $autoload->addPath('/lib/');
?>
