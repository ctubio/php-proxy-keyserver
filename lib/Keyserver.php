<?php namespace PhpProxySks;

use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Keyserver {
  public static function getResponse() {
    $config = Config::getInstance(
      $request = Request::createFromGlobals()
    );

    $errno = (isset($_GET['ERRNO']) and !empty($_GET['ERRNO']))
      ? (int)$_GET['ERRNO'] : FALSE;

    $response = $errno
      ? new Response(NULL, $errno, array('Content-Type' => 'text/html'))
      : ($config->is_hkp_uri
          ? Factory::forward($request)->to(
              'http://'.$config->hkp_addr.':'.$config->hkp_port
              .$config->uri
            )
          : new Response(
              Phtml::parse($config->uri),
              Response::HTTP_OK,
              array('Content-Type' => 'text/html')
            )
        );

    if (($errno = ($errno ?: (int)$response->getStatusCode())) !== 200)
      $response->setContent(Phtml::parse($errno));
    
    return $response;
  }
}
