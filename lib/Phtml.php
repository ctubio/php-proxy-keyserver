<?php namespace PhpProxySks;

class Phtml {

  public static function parse($phtml) {
    if (!is_readable($phtml))
      return NULL;
    $config = Config::getInstance();
    ob_start();
    include($phtml);
    return ob_get_clean();
  }
}
