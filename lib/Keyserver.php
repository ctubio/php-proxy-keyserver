<?php namespace PhpProxySks;

use PhpProxySks\Keyserver\Config;
use PhpProxySks\Keyserver\Skin;
use Symfony\Component\HttpFoundation\Request;

class Keyserver {

  public static function getResponse() {
    return Skin::makeResponse(
      Config::getInstance(
        $request = Request::createFromGlobals()
      ),
      $request
    );
  }
}
