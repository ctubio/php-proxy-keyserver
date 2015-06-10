<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;
use ctubio\HKPProxy\Keyserver\Skin;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Response;

class Router {

  public static function getResponse() {
    $response = strpos($uri = preg_replace('/^\/$/', '/index',
      Keyserver::getRequest()->server->get('REQUEST_URI')
    ), '/pks/') === 0
      ? Skin::parseContent(self::getHKPResponse($uri))
      : Skin::parsePhtml(new Response(), strtok($uri,'?'));

    if (($errno = (int)$response->getStatusCode()) !== 200)
      $response = Skin::parsePhtml($response, '/errors/'.$errno);

    return $response;
  }

  public static function getHKPResponse($uri) {
    $config = Keyserver::getConfig();

    try {
      return Factory::forward(Keyserver::getRequest())->to(
        'http://'.$config->hkp_addr.':'.$config->hkp_port.$uri
      );
    } catch (\Exception $e) {
      return new Response(
        Log::catchError($e, 'Double-check if the keyserver is up and running at the expected address:port ('.$config->hkp_addr.':'.$config->hkp_port.').')
      );
    }
  }
}
