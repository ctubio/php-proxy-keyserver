<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;

class Log {

  public static function catchError($e) {
    $e = $e->getMessage().PHP_EOL.$e->getTraceAsString();

    if ((bool)(int)Keyserver::getConfig()->display_errors)
      die('<pre>'.$e.'</pre>');

    if (!$path=realpath('../log'))
      @mkdir($path=realpath('..').'/log',0777,TRUE);
    $path .= '/php-proxy-sks.log';
    @file_put_contents($path, $e.PHP_EOL
      .(file_exists($path)?@file_get_contents($path):NULL)
    );
  }
}
