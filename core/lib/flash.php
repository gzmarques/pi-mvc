<?php class Flash  {
  /*
   * Flash message
   * A variável flash permite armazenar mensagens durante apenas uma mudança de página.
   * Excelente para avisos e alertas
   */
  public static function message($key=null, $value = null) {
    $index = 'flash' . sha1('flash');
    if (isset($key)) {
      if (isset($value)){
        $_SESSION[$index][$key] = $value;
      }else{
        $val = isset($_SESSION[$index][$key]) ? $_SESSION[$index][$key] :'';
        unset($_SESSION[$index][$key]);
        return $val;
      }
    }else{
      $flashs = isset($_SESSION[$index]) ? $_SESSION[$index] : array();
      unset($_SESSION[$index]);
      return $flashs;
    }
  }
} ?>
