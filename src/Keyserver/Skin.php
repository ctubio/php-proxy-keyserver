<?php namespace PhpProxy\Keyserver;

use PhpProxy\Keyserver;
use PhpProxy\Keyserver\Log;
use PhpProxy\Keyserver\Skin\MimeType;
use PhpProxy\Keyserver\Skin\Content\Phtml;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function _isPhtml() {
    return file_exists(self::getPath().'/skin_layout.phtml');
  }

  public static function getPath($skin = FALSE) {
    return '../skin/'.($skin ?: Keyserver::getConfig()->skin_path);
  }

  public static function parsePhtml(Response $response, $phtml) {
    return (self::_isPhtml() and file_exists(
        self::getPath().'/pages/'.ltrim($phtml, '/').'.phtml'
    )) ? self::parseContent($response, (string)new Phtml($phtml))
      : self::parseNonPhtml($response, $phtml);
  }

  public static function parseNonPhtml(Response $response, $file) {
    if (!file_exists(
      $file = realpath(file_exists($file=self::getPath().$file)
        ? $file : (file_exists($file.'.html')
          ? $file.'.html' : $file.'.xhtml')
    ))) {
      if ($response->getStatusCode() == 200) $response->setStatusCode(404);
      $response->setContent($file = (string)new Phtml(
        '/errors/'.$response->getStatusCode(), FALSE, 'default'
      ));
    } else {
      if (strpos($file=realpath($file), realpath(Skin::getPath()))!==0)
        throw new \Exception('Unknown skin path: "'.$file.'".');

      $response->headers->set('Content-Type', MimeType::get($file));
      $response->setContent($file=file_get_contents($file));
    }

    $response->headers->set('Content-Length', strlen($file));

    return $response;
  }

  public static function parseContent(Response $response, $content = FALSE) {
    $response->headers->set('Via',
      '1.1 '.Keyserver::getConfig()->hostname
      .':'.Keyserver::getConfig()->hkp_port
      .' ('.(Keyserver::getConfig()->expose_keyserver
        ? $response->headers->get('Server') : 'php-proxy-keyserver'
      ).')'
    );

    if (strpos($response->headers->get('Content-Disposition'), 'attachment')===0
     or !Keyserver::getRequest()->server->get('HTTP_USER_AGENT')
     or (Keyserver::getRequest()->server->get('SERVER_PORT') === Keyserver::getConfig()->hkp_port
      and !Keyserver::getConfig()->layout_hkp_request
      and strpos(Keyserver::getRequest()->server->get('REQUEST_URI'), '/pks/') === 0))
      return $response;

    if (!$content) $content = self::_isPhtml()
      ? (string)new Phtml(FALSE, $response->getContent())
      : $response->getContent();

    if (self::_isPhtml() and Keyserver::getConfig()->indent_strict_html)
      $content = self::_indentStrictHtml($content);

    $response->headers->set('Content-Type', 'text/html;charset=UTF-8');
    $response->headers->set('Content-Length', strlen($content));

    return $response->setContent($content);
  }

  public static function _indentStrictHtml($content) {
    $dom = new \DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    libxml_use_internal_errors(true);
    if (!$dom->loadXML(utf8_encode($content), LIBXML_PARSEHUGE)) {
      $_error = "Validation of Strict HTML failed:";
      foreach(libxml_get_errors() as $error)
        $_error .= "\n\t".$error->message;
      return Log::catchError($_error) ?: $content;
    }
    return preg_replace('~></(?:area|base(?:font)?|br|col|command|embed|frame|hr|img|input|keygen|link|meta|param|source|track|wbr)>~', '/>',
      utf8_decode(substr($dom = $dom->saveXML($dom, LIBXML_NOEMPTYTAG), strpos($dom, '?'.'>') + 3))
    );
  }
}
