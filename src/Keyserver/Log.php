<?php namespace PhpProxy\Keyserver;

use PhpProxy\Keyserver;

class Log {

  public static function catchError($e, $hint = NULL) {
    if (!is_string($e))
      $e = $e->getMessage().PHP_EOL.$e->getTraceAsString();

    if ($hint) $e = 'Hint! '.$hint.PHP_EOL.$e;

    if ((bool)(int)Keyserver::getConfig()->display_exceptions)
      echo '<pre>', $e, '</pre>';

    if (!$path=realpath('../log'))
      @mkdir($path=realpath('..').'/log',0777,TRUE);
    $path .= '/php-proxy-keyserver.log';
    @file_put_contents($path, $e.PHP_EOL
      .(file_exists($path)?@file_get_contents($path):NULL)
    );
  }
}
