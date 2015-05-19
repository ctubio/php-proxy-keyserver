<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver\Skin\Phtml;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function wrapContent(Response $response, $forcedContent = FALSE) {
    $response->headers->set('Content-Type', 'text/html');
    
    if ($forcedContent) $response->setContent($forcedContent);
    
    return $response;
  }
  
  public static function getPhtml(Response $response, $phtml) {
    return self::wrapContent($response, (string)new Phtml($phtml));
  }
}
