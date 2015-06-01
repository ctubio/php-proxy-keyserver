<?php namespace PhpProxy\Keyserver\Skin;

use PhpProxy\Keyserver;
use PhpProxy\Keyserver\Log;
use PhpProxy\Keyserver\Skin;

class Phtml {

  private $_page;
  private $_content;
  private $_hkp_style;

  public function __construct($page, $content = FALSE, $skin = FALSE) {
    $this->_page = (string)$page;
    if ($content)
      $this->_content = $this->_importContent($content);
    $this->_skin = (string)$skin;
  }

  public function __toString() {
    try {
      return $this->_importData($this->_parsePhtml(Skin::getPath($this->_skin).((
        strpos($this->_page, '/errors/')===0
        and !Keyserver::getConfig()->layout_html_errors
      ) ? '/plain_'.ltrim($this->_page,'/') : '/skin_layout' ).'.phtml'));
    } catch (\Exception $e) {
      return Log::catchError($e);
    }
  }

  public function _importContent($content) {
    if (substr(trim($content), 0, 1)!=='<')
      return '<pre>'.htmlentities($content).'</pre>';

    $dom = new \DOMDocument('1.0');
    libxml_use_internal_errors(true);
    if (!$dom->loadHTML(utf8_encode($content), LIBXML_PARSEHUGE)) {
      $_error = "Validation of Strict HTML in Keyserver's output failed:";
      foreach(libxml_get_errors() as $error)
        $_error .= "\n\t".$error->message;
      return Log::catchError($_error)
        ?: preg_replace('/.*<body>(.*)<\/body>.*$/s', '$1', $content);
    }
    $xpath = new \DOMXPath($dom);
    $this->_exportData($dom, $xpath);
    $body = $xpath->query('/html/body');
    $content = preg_replace('/^<body>(.*)<\/body>$/s', '$1',
      utf8_decode($dom->saveXml($body->item(0)))
    );

    if (Keyserver::getConfig()->repair_hpk_h1_tags)
      $content = preg_replace('/<h1>(Public Key Server -- )?(.*?)(( "| \')(.*?)( "|\')?)?<\/h1>/s', '<h2>$2:'.(Keyserver::getRequest()->query->get('search')?' <i>'.Keyserver::getRequest()->query->get('search').'</i>':NULL).'</h2>',
        preg_replace('/<h2>(.*)<\/h2>/', '<h3>$1</h3>', preg_replace('/<h3>(.*)<\/h3>/', '<h4>$1</h4>',
          $content
      )));

    return $content;
  }

  private function _parsePhtml($file) {
    if (strpos(realpath($file), realpath(Skin::getPath($this->_skin)))!==0)
      throw new \Exception('Unknown skin path: "'.$file.'".');

    ob_start();
    include($file);
    return ob_get_clean();
  }

  public function _exportData($dom, $xpath) {
    $this->_hkp_style = utf8_decode($dom->saveXml($xpath->query('/html/head/style')->item(0)));
  }

  private function _importData($content) {
    if ($this->_hkp_style) {
      $content = str_replace('<![CDATA[', NULL, str_replace(']]>', NULL,str_replace('/*<![CDATA[*/', NULL, str_replace('/*]]]]><![CDATA[>*/', NULL,
        preg_replace('/(<\/head>)/s', $this->_hkp_style.'$1', $content)
      ))));
    }

    return $content;
  }

  private function getConfig($key) {
    if (!property_exists($config = Keyserver::getConfig(), $key))
      throw new \Exception('Unknown config: "'.$key.'".');

    return $config->{$key};
  }

  private function getBlock($phtml) {
    if (!is_readable($file = realpath(
      $path = Skin::getPath($this->_skin).'/blocks/'.$phtml.'.phtml'
    )))
      throw new \Exception('Unknown block: "'.$path.'".');

    return $this->_parsePhtml($file);
  }

  private function getPage($phtml = NULL) {
    if (is_null($phtml)) {
      if (!$this->_page and $this->_content)
        return $this->_content;
      $phtml = $this->_page;
    }

    if (!is_readable($file = realpath(
      $path = Skin::getPath($this->_skin).'/pages/'.ltrim($phtml, '/').'.phtml'
    )))
      throw new \Exception('Unknown page: "'.$path.'".');

    return $this->_parsePhtml($file);
  }
}
