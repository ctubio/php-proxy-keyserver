<?php namespace PhpProxySks;

use PhpProxySks\Config;
use Symfony\Component\HttpFoundation\Request;

class Config {
  public function __construct(Request $request) {
    foreach(
      array_merge(
        array(
          'hkp_port' => '11371',
          'hkp_addr' => $request->server->get('SERVER_ADDR'),
          'hostname' => $request->server->get('SERVER_NAME'),
          'contact_email' => 'bugs@'.$request->server->get('SERVER_NAME')
        ),
        parse_ini_file(realpath('../etc/php-proxy-sks.ini'))
      ) as $k => $v
    ) $this->{$k} = $v;
    $this->hkp_uri = $request->server->get('REQUEST_URI');
  }
}
