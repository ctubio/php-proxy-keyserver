<?php namespace ctubio\HKPProxy;

use ctubio\HKPProxy\Keyserver\Config;
use ctubio\HKPProxy\Keyserver\Router;
use Symfony\Component\HttpFoundation\Request;

class Keyserver {

  public static $request_instance;

  public static function getRequest() {
    if (self::$request_instance === NULL)
      self::$request_instance = Request::createFromGlobals();

    return self::$request_instance;
  }

  public static function getResponse() {
    return Router::getResponse();
  }

  public static function getConfig() {
    return Config::getInstance();
  }
}
