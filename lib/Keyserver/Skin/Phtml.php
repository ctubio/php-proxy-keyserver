<?php namespace PhpProxySks\Keyserver\Skin;

use PhpProxySks\Keyserver;

class Phtml {

  private $content;
  
  public function __construct($phtml) {
    $this->content = (is_readable($file = realpath(
      $this->getSkinPath().$phtml.'.phtml'
    ))) ? $this->parsePhtml($file)
        : 'Err'.(is_numeric(basename($phtml)) ? 'no' : 'or').': '.$phtml;
  }
  
  public function __toString() {
    return $this->content;
  }

  private function getSkinPath() {
    return '../skin/'.Keyserver::getConfig()->html_skin;
  }
  
  private function getBlock($phtml) {
    if (!is_readable($file = realpath(
      $path = $this->getSkinPath().'/block/'.$phtml.'.phtml'
    )))
      throw new \Exception('Unknown block: "'.$path.'".');
    return $this->parsePhtml($file);
  }
  
  private function getConfig($key) {
    if (!property_exists($config = Keyserver::getConfig(), $key))
      throw new \Exception('Unknown config: "'.$key.'".');
    return $config->{$key};
  }
  
  private function parsePhtml($file) {
    if (strpos(realpath($file), realpath($this->getSkinPath()))!==0)
      throw new \Exception('Unknown skin path: '.$file.'.');
    ob_start();
    include($file);
    return ob_get_clean();
  }
}
