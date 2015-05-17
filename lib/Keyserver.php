<?php namespace PhpProxySks;

use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Keyserver {
  public static function getResponse() {
    $config = Config::getInstance(
      $request = Request::createFromGlobals()
    );

    $response = (isset($_GET['errno']))
      ? new Response(NULL, (int)$_GET['errno'], array('Content-Type' => 'text/html'))
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
          ? Phtml::parse($error)
          : 'Errno: '.$errno
      );

    return $response;
  }
}
