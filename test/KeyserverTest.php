<?php
use PhpProxy\Keyserver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class KeyserverTest extends PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'GNU/Linux Inside!'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Submit this key'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Remove my key!'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Please send bug reports'));
    }

    public function testFaq()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/doc/faq');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'GNU/Linux Inside!'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Can you delete my key from the key server?'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'No.'));
      $this->assertSame(FALSE, strpos($response->getContent(), 'Remove my key!'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Please send bug reports'));
    }

    public function test404()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/doc/missing');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
    }

    public function testRobots()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/robots.txt');
      Keyserver::$request_instance = $request;
      file_put_contents('../skin/default/robots.txt', file_get_contents('../pub/robots.txt'));
      $response = Keyserver::getResponse();
      unlink('../skin/default/robots.txt');

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/plain', $response->headers->get('content-type'));
      $this->assertSame(0, strpos($response->getContent(), 'User-agent: *'));
      $this->assertGreaterThan(10, strpos($response->getContent(), 'Disallow: /pks/'));
    }

    public function testFavicon()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/favicon.ico');
      Keyserver::$request_instance = $request;
      file_put_contents('../skin/default/favicon.ico', file_get_contents('../pub/favicon.ico'));
      $response = Keyserver::getResponse();
      unlink('../skin/default/favicon.ico');

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertContains($response->headers->get('content-type'), array('image/x-icon', 'image/png'));
      $this->assertSame(1, strpos($response->getContent(), 'PNG'));
      $this->assertEquals(193, strlen($response->getContent()));
    }
}
