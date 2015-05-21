<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;
use PhpProxySks\Keyserver\Skin;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Response;

class Router {

  public static function getResponse() {
    $config = Keyserver::getConfig();

    try {
      $response = ($errno = Keyserver::getErrno())
        ? new Response(NULL, $errno)
        : (strpos($uri = Keyserver::getUri(), '/pks/') === 0
            ? Skin::parseContent(Factory::forward(Keyserver::getRequest())->to(
                'http://'.$config->hkp_addr.':'.$config->hkp_port.$uri
              ))
            : Skin::parsePhtml(new Response(), $uri)
          );
    } catch (\Exception $e) {
      Log::catchError($e, 'Double-check if the keyserver is up and running at the expected address:port ('.$config->hkp_addr.':'.$config->hkp_port.').');
    }

    if (($errno = ($errno ?: (int)$response->getStatusCode())) !== 200)
      $response = Skin::parsePhtml($response, '/errors/'.$errno);

    return $response;
  }
}
