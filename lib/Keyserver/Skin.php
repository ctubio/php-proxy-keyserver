<?php namespace PhpProxySks\Keyserver;

use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpProxySks\Keyserver\Config;

class Skin {

  public static function makeResponse(Config $config, Request $request) {
    $response = $config->request_errno
      ? new Response(NULL, $config->request_errno)
      : ($config->is_hkp_request_uri
          ? self::wrapContent(Factory::forward($request)->to(
              'http://'.$config->hkp_addr.':'.$config->hkp_port
              .$config->request_uri
            ))
          : self::setContent(new Response, $config->request_uri)
        );

    if (($config->request_errno = (
      $config->request_errno ?: (int)$response->getStatusCode()
    )) !== 200)
      $response = self::setContent($response, '/errno/'.$config->request_errno);

    return $response;
  }

  public static function wrapContent(Response $response, $content = NULL) {
    $response->headers->set('Content-Type', 'text/html');
    if (!is_null($content))
      $response->setContent($content);
    return $response;
  }

  public static function setContent(Response $response, $phtml) {
    $config = Config::getInstance();

    if (!is_readable($file = realpath('../skin/'.$config->html_skin.$phtml.'.phtml')))
      $content = 'Err'.(is_numeric(basename($phtml))?'no':'or').': '.$phtml;
    else {
      ob_start();
      include($file);
      $content = ob_get_clean();
    }

    return self::wrapContent($response, $content);
  }
}
