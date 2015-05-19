<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;
use PhpProxySks\Keyserver\Skin;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Response;

class Router {

  public static function getResponse() {
    $config = Keyserver::getConfig();

    $response = ($errno = Keyserver::getErrno())
      ? new Response(NULL, $errno)
      : (strpos($uri = Keyserver::getUri(), '/pks/') === 0
          ? Skin::wrapContent(Factory::forward(Keyserver::getRequest())->to(
              'http://'.$config->hkp_addr.':'.$config->hkp_port.$uri
            ))
          : Skin::getPhtml(new Response(), $uri)
        );

    if (($errno = ($errno ?: (int)$response->getStatusCode())) !== 200)
      $response = Skin::getPhtml($response, '/errors/'.$errno);

    return $response;
  }
}
