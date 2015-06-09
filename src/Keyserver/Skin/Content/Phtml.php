<?php namespace PhpProxy\Keyserver\Skin\Content;

use PhpProxy\Keyserver;
use PhpProxy\Keyserver\Skin;
use PhpProxy\Keyserver\Skin\Content;

class Phtml extends Content {
  protected function getConfig($key) {
    if (!property_exists($config = Keyserver::getConfig(), $key))
      throw new \Exception('Unknown config: "'.$key.'".');

    return $config->{$key};
  }

  protected function getBlock($phtml) {
    if (!is_readable($file = realpath(
      $path = Skin::getPath($this->_skin).'/blocks/'.$phtml.'.phtml'
    )))
      throw new \Exception('Unknown block: "'.$path.'".');

    return $this->parsePhtml($file);
  }

  protected function getPage($phtml = NULL) {
    if (is_null($phtml)) {
      if (!$this->_page && $this->_content)
        return $this->_content;
      $phtml = $this->_page;
    }

    if (!is_readable($file = realpath(
      $path = Skin::getPath($this->_skin).'/pages/'.ltrim($phtml, '/').'.phtml'
    )))
      throw new \Exception('Unknown page: "'.$path.'".');

    return $this->parsePhtml($file);
  }
}
