<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;
use PhpProxySks\Keyserver\Skin\Phtml;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function parsePhtml(Response $response, $phtml) {
    return self::parseContent($response, (string)new Phtml($phtml));
  }

  public static function parseContent(Response $response, $content = FALSE) {
    if (strpos($response->headers->get('content-disposition'), 'attachment')===0)
      return $response;

    if (!$content) $content = self::_importContent($response);

    $content = utf8_encode($content);

    if (Keyserver::getConfig()->indent_strict_html)
      $content = self::_indentStrictHtml($content);

    $response->headers->set('content-type', 'text/html;charset=UTF-8');
    $response->headers->set('content-length', strlen($content));

    return $response->setContent($content);
  }

  public static function _importContent(Response $response) {
    $content = $response->getContent();
    return $content;
  }

  public static function _indentStrictHtml($content) {
    $dom = new \DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($content);
    return preg_replace('~></(?:area|base(?:font)?|br|col|command|embed|frame|hr|img|input|keygen|link|meta|param|source|track|wbr)>~', '/>',
      substr($dom = $dom->saveXML($dom, LIBXML_NOEMPTYTAG), strpos($dom, '?'.'>') + 3)
    );
  }
}
