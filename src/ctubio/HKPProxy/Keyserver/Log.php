<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log {

  public static $monolog_instance;

  public static function getLog() {
    if (self::$monolog_instance === NULL) {
      if (!$path=realpath('../log'))
        mkdir($path=realpath('..').'/log',0777,TRUE);
      self::$monolog_instance = new Logger('php-proxy-keyserver');
      $handler = new StreamHandler($path.'/'.self::$monolog_instance->getName().'.log', Logger::EMERGENCY);
      $handler->setFormatter(new LineFormatter(NULL, NULL, TRUE));
      self::$monolog_instance->pushHandler($handler);
    }

    return self::$monolog_instance;
  }

  public static function catchError($e, $hint = NULL) {
    if (!is_string($e))
      $e = $e->getMessage().PHP_EOL.$e->getTraceAsString();

    if ($hint) $e = 'Hint! '.$hint.PHP_EOL.$e;

    if (!(bool)(int)Keyserver::getConfig()->display_exceptions) {
      self::getLog()->addEmergency($e);
      return "An error ocurred. Please, read the logs or contact the keyserver administrator.";
    }

    return '<pre>' . $e . '</pre>';
  }
}
