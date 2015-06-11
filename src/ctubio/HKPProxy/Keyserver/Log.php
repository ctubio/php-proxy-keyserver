<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;

class Log {

  public static function catchError($e, $hint = NULL) {
    if (!is_string($e))
      $e = $e->getMessage().PHP_EOL.$e->getTraceAsString();

    if ($hint) $e = 'Hint! '.$hint.PHP_EOL.$e;

    if (!(bool)(int)Keyserver::getConfig()->display_exceptions) {
      if (!$path=realpath('../log'))
        mkdir($path=realpath('..').'/log',0777,TRUE);
      $path .= '/php-proxy-keyserver.log';
      file_put_contents($path, 'EXCEPTION '.date('Y-m-d H:i:s').': '.$e.PHP_EOL
        .(file_exists($path)?file_get_contents($path):NULL)
      );
      return "An error ocurred. Please, read the logs or contact the keyserver administrator.";
    }

    return '<pre>' . $e . '</pre>';
  }
}
