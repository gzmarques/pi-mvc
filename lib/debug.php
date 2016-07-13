<?php
class Debug {

  public static function log() {
    $args = func_get_args();
    $output = "";

    $output = '<div id="debug" style="background-color: rgba(211, 211, 211, 0.28);padding: 10px;border-radius: 5px;"><h1>Debug:</h1>';

    $print_r = "<pre>";
    $var_dump = "<pre>";

    foreach ($args as $arg) {
      ob_start();
      var_dump($arg);
      $var_dump .= ob_get_clean() . '<hr>' ;
      $print_r .= print_r($arg, TRUE) . '<hr>' ;
    }

    $print_r .= "</pre>";
    $var_dump .= "</pre>";
    $output .= '<h2> print_r: </h2>';
    $output .= $print_r . '<h2> var_dump: </h2>';
    $output .= $var_dump . '</div>';

    print($output);
    // exit();
  }


} ?>
