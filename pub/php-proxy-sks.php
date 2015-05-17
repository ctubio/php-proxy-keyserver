<?php
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;

require '../vendor/autoload.php';

$request = Request::createFromGlobals();

$config = parse_ini_file(realpath('../etc/php-proxy-sks.ini'));
$config = (object)array_merge(
  array(
    'hkp_port' => '11371',
    'hkp_address' => $request->server->get('SERVER_ADDR')
  ),
  parse_ini_file($config['sksconf'])
);
$config->hkp_address = current(explode(' ', $config->hkp_address));

if (!isset($_GET['errno']))
  $response = Factory::forward($request)->to(
    'http://'.$config->hkp_address.':'.$config->hkp_port
    .$request->server->get('REQUEST_URI')
  );

if (
 ($errno = isset($_GET['errno'])
   ? $_GET['errno'] : $response->getStatusCode()
 ) !== '200'
 and is_numeric($errno)
 and is_readable($error = realpath($errno.'.html'))
) {
  $response->setContent(file_get_contents($error));
}

$response->send();

