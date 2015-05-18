<?php namespace PhpProxySks;

use PhpProxySks\Keyserver\Config;
use PhpProxySks\Keyserver\Router;
use Symfony\Component\HttpFoundation\Request;

class Keyserver {
  
  public static $request_instance;
  
  public static function getRequest() {
    if (self::$request_instance === NULL)
      self::$request_instance = Request::createFromGlobals();
    return self::$request_instance;
  }

  public static function getErrno() {
    return (int)self::getRequest()->query->get('ERRNO');
  }

  public static function getUri() {
    return preg_replace('/^\/$/', '/index',
      self::getRequest()->server->get('REQUEST_URI')
    );
  }
  public static function getConfig() {
    return Config::getInstance();
  }

  public static function getResponse() {
    return Router::getResponse();
  }
}
