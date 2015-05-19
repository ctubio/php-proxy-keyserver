<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver\Skin\Phtml;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function wrapContent(Response $response, $forcedContent = FALSE) {
    $response->headers->set('Content-Type', 'text/html');
    
    if ($forcedContent) $response->setContent($forcedContent);
    
    return $response;
  }
  
  public static function getPage(Response $response, $phtml) {
    return self::wrapContent($response, (string)new Phtml('/pages'.$phtml));
  }

  public static function getError(Response $response, $phtml) {
    return self::getPage($response, '/errors/'.$phtml);
  }
}
