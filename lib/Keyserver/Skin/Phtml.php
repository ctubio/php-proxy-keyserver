<?php namespace PhpProxySks\Keyserver\Skin;

use PhpProxySks\Keyserver;
use PhpProxySks\Keyserver\Log;

class Phtml {

  private $_page;

  public function __construct($page) {
    $this->_page = $page;
  }

  public function __toString() {
    try {
      return $this->_parsePhtml($this->_getSkinPath().((
        strpos($this->_page, '/errors/')===0
        and !Keyserver::getConfig()->layout_404
      ) ? '/plain_'.ltrim($this->_page,'/') : '/skin_layout' ).'.phtml');
    } catch (\Exception $e) {
      Log::catchError($e);
      return "";
    }
  }

  private function _getSkinPath() {
    return '../skin/'.Keyserver::getConfig()->html_skin;
  }

  private function _parsePhtml($file) {
    if (strpos(realpath($file), realpath($this->_getSkinPath()))!==0)
      throw new \Exception('Unknown skin path: "'.$file.'".');

    ob_start();
    include($file);
    return ob_get_clean();
  }

  private function getConfig($key) {
    if (!property_exists($config = Keyserver::getConfig(), $key))
      throw new \Exception('Unknown config: "'.$key.'".');

    return $config->{$key};
  }

  private function getBlock($phtml) {
    if (!is_readable($file = realpath(
      $path = $this->_getSkinPath().'/blocks/'.$phtml.'.phtml'
    )))
      throw new \Exception('Unknown block: "'.$path.'".');

    return $this->_parsePhtml($file);
  }

  private function getPage($phtml = NULL) {
    if (is_null($phtml)) $phtml = $this->_page;

    if (!is_readable($file = realpath(
      $path = $this->_getSkinPath().'/pages/'.ltrim($phtml, '/').'.phtml'
    )))
      throw new \Exception('Unknown page: "'.$path.'".');

    return $this->_parsePhtml($file);
  }
}
