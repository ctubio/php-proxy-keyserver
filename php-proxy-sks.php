<?php
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;

require 'vendor/autoload.php';

$request = Request::createFromGlobals();

$response = Factory::forward($request)->to(
  'http://'.$request->server->get('SERVER_ADDR').':11371'
  .$request->server->get('REQUEST_URI')
);


if (
 ($statusCode = $response->getStatusCode()) !== '200'
 and is_readable($statusCode.'.html')
) {
  $response->setContent(file_get_contents(
    $statusCode.'.html'
  ));
}

$response->send();