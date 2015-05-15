<?php
use Proxy\Factory;
use Proxy\Response\Filter\RemoveEncodingFilter;
use Symfony\Component\HttpFoundation\Request;

require 'vendor/autoload.php';

// Create a Symfony request based on the current browser request.
$request = Request::createFromGlobals();

// Forward the request and get the response.
$response = Factory::forward($request)->to(
  'http://'.$request->server->get('SERVER_ADDR').':11371'
  .$request->server->get('REQUEST_URI')
);

// Output response to the browser.
$response->send();
