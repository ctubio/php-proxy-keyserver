<?php namespace PhpProxySks;

use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpProxySks\Config;

class Keyserver {
  public static function getResponse() {
    $config = Config::getInstance(
      $request = Request::createFromGlobals()
    );

    $response = (isset($_GET['errno']))
      ? new Response(NULL, $_GET['errno'], array('content-type' => 'text/html'))
      : Factory::forward($request)->to(
          'http://'.$config->hkp_addr.':'.$config->hkp_port
          .$config->hkp_uri
        );

    if (($errno = isset($_GET['errno'])
       ? (int)$_GET['errno']
       : (int)$response->getStatusCode()
     ) !== 200)
      $response->setContent(
        (is_readable($error = realpath('../lib/phtml/'.$errno.'.phtml')))
          ? self::parsePhtml($error)
          : 'Errno: '.$errno
      );

    return $response;
  }

  public static function parsePhtml($phtml) {
    if (!is_readable($phtml))
      return NULL;
    $config = Config::getInstance();
    ob_start();
    include($phtml);
    return ob_get_clean();
  }
}
