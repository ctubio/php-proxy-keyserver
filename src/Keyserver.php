<?php namespace PhpProxy;

use PhpProxy\Keyserver\Config;
use PhpProxy\Keyserver\Router;
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
