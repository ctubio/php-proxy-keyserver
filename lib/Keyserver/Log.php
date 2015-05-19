<?php namespace PhpProxySks\Keyserver;

use PhpProxySks\Keyserver;

class Log {

  public static function catchError($e) {
    $error = $e->getMessage().PHP_EOL.$e->getTraceAsString();

    if ((bool)(int)Keyserver::getConfig()->debug_mode)
      die('<pre>'.$error.'</pre>');

    if (!$path=realpath('../log'))
      @mkdir($path=realpath('..').'/log',0777,TRUE);
    $path .= '/php-proxy-sks.log';
    @file_put_contents($path, $error.PHP_EOL
      .(file_exists($path)?@file_get_contents($path):NULL)
    );
    @header('Location:/error');
  }
}
