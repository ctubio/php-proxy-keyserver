<?php namespace PhpProxySks;

class Phtml {

  public static function parse($phtml) {
    if (!is_readable($file = realpath('../lib/phtml/'.$phtml.'.phtml')))
      return 'Err'.(is_numeric($phtml)?'no':'or').': '.$phtml;
    $config = Config::getInstance();
    ob_start();
    include($file);
    return ob_get_clean();
  }
}
