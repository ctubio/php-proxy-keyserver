<?php namespace PhpProxy\Keyserver;

use PhpProxy\Keyserver;
use PhpProxy\Keyserver\Skin;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Response;

class Router {

  public static function getResponse() {
    $response = ($errno = Keyserver::getErrno())
      ? new Response(NULL, $errno)
      : (strpos($uri = Keyserver::getUri(), '/pks/') === 0
          ? Skin::parseContent(self::_getHKPResponse($uri))
          : Skin::parsePhtml(new Response(), $uri)
        );

    if (($errno = ($errno ?: (int)$response->getStatusCode())) !== 200)
      $response = Skin::parsePhtml($response, '/errors/'.$errno);

    return $response;
  }

  public static function _getHKPResponse($uri) {
    $config = Keyserver::getConfig();

    try {
      return Factory::forward(Keyserver::getRequest())->to(
        'http://'.$config->hkp_addr.':'.$config->hkp_port.$uri
      );
    } catch (\Exception $e) {
      Log::catchError($e, 'Double-check if the keyserver is up and running at the expected address:port ('.$config->hkp_addr.':'.$config->hkp_port.').');
    }
  }
}
