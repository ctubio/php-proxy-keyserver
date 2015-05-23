<?php namespace PhpProxy\Keyserver;

use PhpProxy\Keyserver;
use Symfony\Component\HttpFoundation\Request;

class Config {

  public static $instance;

  public static function getInstance() {
    if (self::$instance === NULL)
      self::$instance = new self(Keyserver::getRequest());
    return self::$instance;
  }

  public function __construct(Request $request) {
    foreach(
      array_merge(
        array(
          'hkp_port' => '11371',
          'hkp_addr' => '127.0.0.1',
          'hostname' => $request->server->get('SERVER_NAME'),
          'contact_email' => 'bugs@'.$request->server->get('SERVER_NAME'),
          'html_title' => 'PGP Public Key Server',
          'html_skin' => 'default',
          'layout_html_errors' => 0,
          'indent_strict_html' => 0,
          'display_exceptions' => 0
        ),
        parse_ini_file(realpath('../etc/php-proxy-keyserver.ini'))
      ) as $k => $v
    ) $this->{$k} = $v;

    self::$instance = $this;
  }
}
