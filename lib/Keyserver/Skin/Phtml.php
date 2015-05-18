<?php namespace PhpProxySks\Keyserver\Skin;

use PhpProxySks\Keyserver;

class Phtml {

  private $content;
  
  public function __construct($phtml) {
    if (is_readable($file = realpath(
      '../skin/'.Keyserver::getConfig()->html_skin.$phtml.'.phtml'
    )))
      $this->content = $this->parsePhtml($file);
    else
      $this->content = 'Err'.(
        is_numeric(basename($phtml)) ? 'no' : 'or'
      ).': '.$phtml;
  }
  
  public function __toString() {
    return $this->content;
  }

  private function getBlock($phtml) {
    if (is_readable($file = realpath(
      '../skin/'.Keyserver::getConfig()->html_skin.'/block/'.$phtml.'.phtml'
    )))
      return $this->parsePhtml($file);
    else throw Exception('Unknown config: '.$key);
  }
  
  private function get($key) {
    if (property_exists($config = Keyserver::getConfig(), $key))
      return $config->{$key};
    else throw Exception('Unknown config: '.$key);
  }
  
  private function parsePhtml($file) {
    ob_start();
    include($file);
    return ob_get_clean();
  }
}
