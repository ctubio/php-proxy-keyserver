<?php namespace PhpProxySks;

use Symfony\Component\HttpFoundation\Request;

class Config {
  
  public static $instance;

  public static function getInstance(Request $request = NULL) {
    if (self::$instance === NULL)
      self::$instance = new self($request);
    return self::$instance;
  }
  
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
    
    self::$instance = $this;
  }
}
