<?php namespace PhpProxy\Keyserver;

use PhpProxy\Keyserver;
use PhpProxy\Keyserver\Skin;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Response;

class Router {

  public static function getResponse() {
    $response = strpos($uri = preg_replace('/^\/$/', '/index',
      Keyserver::getRequest()->server->get('REQUEST_URI')
    ), '/pks/') === 0
      ? Skin::parseContent(self::_getHKPResponse($uri))
      : Skin::parsePhtml(new Response(), $uri);

    if (($errno = (int)$response->getStatusCode()) !== 200)
      $response = Skin::parsePhtml($response, '/errors/'.$errno);

    return $response;
  }

  public static function _getHKPResponse($uri) {
    $config = Keyserver::getConfig();

    try {
      $response = Factory::forward(Keyserver::getRequest())->to(
        'http://'.$config->hkp_addr.':'.$config->hkp_port.$uri
      );
      $response->headers->set('Via', '1.1 '.Keyserver::getConfig()->hostname.':'.Keyserver::getConfig()->hkp_port.' (php-proxy-keyserver)');
      return $response;
    } catch (\Exception $e) {
      return new Response(
        Log::catchError($e, 'Double-check if the keyserver is up and running at the expected address:port ('.$config->hkp_addr.':'.$config->hkp_port.').')
      );
    }
  }
}
