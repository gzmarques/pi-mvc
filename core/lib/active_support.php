<?php class ActiveSupport {
    public static function snakeToCamelCase($value) {
      return preg_replace('/_/', '', $value);
    }

    public static function camelToSnake($value) {
      $pattern = '/([a-z])([A-Z])/';
      return strtolower(preg_replace($pattern, '$1_$2', $value));
    }

    public static function namespaceToPath($value) {
      return str_replace('\\_', '/', $value);
    }
} ?>
