<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;
use PhpProxySks\Keyserver\Log;
use PhpProxySks\Keyserver\Skin\Phtml;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function parsePhtml(Response $response, $phtml) {
    return self::parseContent($response, (string)new Phtml($phtml));
  }

  public static function parseContent(Response $response, $content = FALSE) {
    if (strpos($response->headers->get('content-disposition'), 'attachment')===0)
      return $response;

    if (!$content) $content = (string)new Phtml(FALSE, $response->getContent());

    if (Keyserver::getConfig()->indent_strict_html)
      $content = self::_indentStrictHtml($content);

    $response->headers->set('content-type', 'text/html;charset=UTF-8');
    $response->headers->set('content-length', strlen($content));

    return $response->setContent($content);
  }

  public static function _indentStrictHtml($content) {
    $dom = new \DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    libxml_use_internal_errors(true);
    if (!$dom->loadXML(utf8_encode($content))) {
      $_error = "Validation of Strict HTML failed:";
      foreach(libxml_get_errors() as $error)
        $_error .= "\n\t".$error->message;
      Log::catchError($_error);
      return $content;
    }
    return preg_replace('~></(?:area|base(?:font)?|br|col|command|embed|frame|hr|img|input|keygen|link|meta|param|source|track|wbr)>~', '/>',
      utf8_decode(substr($dom = $dom->saveXML($dom, LIBXML_NOEMPTYTAG), strpos($dom, '?'.'>') + 3))
    );
  }
}
