<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;
use PhpProxySks\Keyserver\Config;
use Symfony\Component\HttpFoundation\Response;

class Skin {

  public static function wrapContent(Response $response, $setContent = FALSE) {
    $response->headers->set('Content-Type', 'text/html');
    
    if ($setContent) $response->setContent($setContent);
    
    return $response;
  }

  public static function setContent(Response $response, $phtml) {
    $config = Keyserver::getConfig();

    if (is_readable(
      $file = realpath('../skin/'.$config->html_skin.$phtml.'.phtml')
    )) {
      ob_start();
      include($file);
      $content = ob_get_clean();
    } else
      $content = 'Err'.(is_numeric(basename($phtml))?'no':'or').': '.$phtml;

    return self::wrapContent($response, $content);
  }
}
