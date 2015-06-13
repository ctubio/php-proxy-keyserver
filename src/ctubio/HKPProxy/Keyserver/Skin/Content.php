<?php namespace ctubio\HKPProxy\Keyserver\Skin;

use ctubio\HKPProxy\Keyserver;
use ctubio\HKPProxy\Keyserver\Log;
use ctubio\HKPProxy\Keyserver\Skin;

abstract class Content {

  protected $_page;
  protected $_content;
  protected $_skin;

  public function __construct($page, $content = FALSE, $skin = FALSE) {
    $this->_page = (string)$page;
    if ($content)
      $this->_content = $this->importContent($content);
    $this->_skin = (string)$skin;
  }

  public function __toString() {
    try {
      return $this->importHead($this->parsePhtml(Skin::getPath($this->_skin).((
        strpos($this->_page, '/errors/')===0
        && !Keyserver::getConfig()->layout_html_errors
      ) ? '/plain_'.ltrim($this->_page,'/') : '/skin_layout' ).'.phtml'));
    } catch (\Exception $e) {
      return Log::catchError($e);
    }
  }

  public function importContent($content) {
    if (substr(trim($content), 0, 1)!=='<')
      return '<pre>'.trim(htmlentities($content)).'</pre>';

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
    $body = $xpath->query('/html/body');
    $content = preg_replace('/^<body>(.*)<\/body>$/s', '$1',
      utf8_decode($dom->saveXml($body->item(0)))
    );

    if (Keyserver::getConfig()->repair_hkp_h1_tags)
      $content = preg_replace('/<h1>(Public Key Server -- )?(.*?)(( "| \')(.*?)( "|\')?)?<\/h1>/s', '<h2>$2:'.(Keyserver::getRequest()->query->get('search')?' <i>'.Keyserver::getRequest()->query->get('search').'</i> <small style="float:right;line-height:15px;font-size:14px;">'.((Keyserver::getRequest()->query->get('op')=='get' || strpos(Keyserver::getRequest()->server->get('ORIGINAL_REQUEST_URI'),'/get/')===0 || strpos(Keyserver::getRequest()->server->get('ORIGINAL_REQUEST_URI'),'/0x')===0)?'<a href="/download/'.Keyserver::getRequest()->query->get('search').'" title="Download">Download</a>':NULL).(((Keyserver::getRequest()->query->get('op')=='get' || strpos(Keyserver::getRequest()->server->get('ORIGINAL_REQUEST_URI'),'/get/')===0 || strpos(Keyserver::getRequest()->server->get('ORIGINAL_REQUEST_URI'),'/0x')===0) && Keyserver::getRequest()->server->get('ORIGINAL_REQUEST_URI')==Keyserver::getRequest()->server->get('REQUEST_URI'))?' | ':NULL).(Keyserver::getRequest()->server->get('ORIGINAL_REQUEST_URI')==Keyserver::getRequest()->server->get('REQUEST_URI')?'<a href="/'.(Keyserver::getRequest()->query->get('op')!='get'?'search/':(strpos(Keyserver::getRequest()->query->get('search'),'0x')!==0?'get/':NULL)).str_replace(' ','+',Keyserver::getRequest()->query->get('search')).'" title="Permalink">Permalink</a>':NULL).'</small>':NULL).'</h2>',
        preg_replace('/<h2>(.*)<\/h2>/', '<h3>$1</h3>', preg_replace('/<h3>(.*)<\/h3>/', '<h4>$1</h4>', preg_replace('/<pre>&#13;(.*)<\/pre>/s', '<pre style="font-size:15px;line-height:17px;">$1</pre>',
          $content
      ))));

    Keyserver::getConfig()->head_title = (preg_match('/<h2>(.*)<\/h2>/', $content, $matches) && isset($matches[1]))
      ? strtok($matches[1], '<').Keyserver::getRequest()->query->get('search') : Keyserver::getConfig()->html_title;

    return $content;
  }

  public function parsePhtml($file) {
    if (strpos(realpath($file), realpath(Skin::getPath($this->_skin)))!==0)
      throw new \Exception('Unknown skin path: "'.$file.'".');

    ob_start();
    require $file;
    return ob_get_clean();
  }

  private function importHead($content) {
    $content = preg_replace('/<title>(.*)<\/title>/',
      '<title>'
      .((preg_match('/<title>(.*)<\/title>/', $content, $matches) && isset($matches[1]) && strip_tags($matches[1]))
        ? Keyserver::getConfig()->html_title.' | '.strip_tags($matches[1]) : (
          (preg_match('/<h2>(.*)<\/h2>/', $content, $matches) && isset($matches[1]))
            ? Keyserver::getConfig()->html_title.' | '.strip_tags($matches[1]) : Keyserver::getConfig()->html_title))
      .'</title>',
      $content);

    $hkp_styles = <<<CSS
<style type="text/css">
      .uid { color: green; text-decoration: underline; }
      .warn { color: red; font-weight: bold; }
    </style>
CSS;

    return preg_replace('/(<\/head>)/s', $hkp_styles.'$1', $content);
  }

  abstract protected function getConfig($key);

  abstract protected function getBlock($phtml);

  abstract protected function getPage($phtml = NULL);
}
