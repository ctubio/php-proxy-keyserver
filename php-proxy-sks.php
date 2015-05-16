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
 $response->getStatusCode()!='200'
 and is_readable($response->getStatusCode().'.html')
)
  readfile($response->getStatusCode().'.html');
else
  $response->send();