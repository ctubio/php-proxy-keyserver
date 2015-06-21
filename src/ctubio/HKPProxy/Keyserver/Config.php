<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;
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
          'hkp_public_port' => '11371',
          'hkp_load_balanced_port' => 0,
          'hkp_load_balanced_addr' => '127.0.0.1',
          'hkp_primary_keyserver_addr' => NULL,
          'hostname' => $request->server->get('SERVER_NAME'),
          'bugs_contact_mail' => 'bugs@'.$request->server->get('SERVER_NAME'),
          'site_contact_link' => NULL,
          'membership_line' => NULL,
          'html_title' => 'PGP Public Key Server',
          'head_title' => NULL,
          'google_verification' => NULL,
          'google_analytics' => NULL,
          'co2_neutral_link' => NULL,
          'skin_path' => 'default',
          'layout_html_errors' => 0,
          'layout_hkp_request' => 0,
          'show_friendly_urls' => 0,
          'repair_hkp_h1_tags' => 0,
          'indent_strict_html' => 0,
          'expose_keyserver' => 0,
          'expose_source' => 0,
          'expose_dump' => 0,
          'expose_pool' => 0,
          'display_exceptions' => 0
        ),
        parse_ini_file(realpath('../etc/php-proxy-keyserver.ini'))
      ) as $k => $v
    ) $this->{$k} = $v;

    self::$instance = $this;
  }
}
