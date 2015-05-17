<?php namespace PhpProxySks;

use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpProxySks\Config;

class Keyserver {

  private $request = NULL;

  private $config = NULL;

  public static function getResponse() {
    $request = Request::createFromGlobals();
    $config = new Config($request);
    
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
          ? file_get_contents($error)
          : 'Errno: '.$errno
      );

    return $response;
  }
}
