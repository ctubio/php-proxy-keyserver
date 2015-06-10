<?php namespace ctubio\HKPProxy\Keyserver;

use Dflydev\ApacheMimeTypes\PhpRepository;
use ctubio\HKPProxy\Keyserver;
use ctubio\HKPProxy\Keyserver\Log;
use ctubio\HKPProxy\Keyserver\Skin\Content\Phtml;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function isPhtml() {
    return file_exists(self::getPath().'/skin_layout.phtml');
  }

  public static function getPath($skin = FALSE) {
    return '../skin/'.($skin ?: Keyserver::getConfig()->skin_path);
  }

  public static function parsePhtml(Response $response, $phtml) {
    return (self::isPhtml() && file_exists(
        self::getPath().'/pages/'.ltrim($phtml, '/').'.phtml'
    )) ? self::parseContent($response, (string)new Phtml($phtml))
      : self::parseNonPhtml($response, $phtml);
  }

  public static function parseNonPhtml(Response $response, $file) {
    if (!file_exists(
      $file = file_exists($file=self::getPath().$file)
        ? $file : (file_exists($file.'.html')
          ? $file.'.html' : (file_exists($file.'.xhtml')
            ? $file.'.xhtml' : (file_exists($file.'.php')
              ? $file.'.php' : $file))
    ))) {
      if ($response->getStatusCode() == 200) $response->setStatusCode(404);
      $response->setContent($file = (string)new Phtml(
        '/errors/'.$response->getStatusCode(), FALSE, 'default'
      ));
    } else {
      if (strpos($file=realpath($file), realpath(Skin::getPath()))!==0)
        throw new \Exception('Unknown skin path: "'.$file.'".');
      $file = str_replace('sitemap.xml.php', 'sitemap.xml', $file);

      $repository = new PhpRepository();
      $response->headers->set('Content-Type', $repository->findType(
        strtolower(substr(strrchr($file, '.'), 1)) ?: $file
      ) ?: 'text/plain');

      if (basename($file)=='sitemap.xml') {
        ob_start();
        require $file.'.php';
        $file = ob_get_clean();
      } else
        $file = file_get_contents($file);

      $response->setContent($file);
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
     || !Keyserver::getRequest()->server->get('HTTP_USER_AGENT')
     || (Keyserver::getRequest()->server->get('SERVER_PORT') === Keyserver::getConfig()->hkp_port
      && !Keyserver::getConfig()->layout_hkp_request
      && strpos(Keyserver::getRequest()->server->get('REQUEST_URI'), '/pks/') === 0))
      return $response;

    if (!$content) $content = self::isPhtml()
      ? (string)new Phtml(FALSE, $response->getContent())
      : $response->getContent();

    if (self::isPhtml() && Keyserver::getConfig()->indent_strict_html)
      $content = self::indentStrictHtml($content);

    $response->headers->set('Content-Type', 'text/html;charset=UTF-8');
    $response->headers->set('Content-Length', strlen($content));

    return $response->setContent($content);
  }

  public static function indentStrictHtml($content) {
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
