<?php
use PhpProxy\Keyserver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class KeyserverTest extends PHPUnit_Framework_TestCase
{
    public function testErrno404()
    {
      $request = Request::createFromGlobals();
      $request->query->set('ERRNO', '404');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
    }

    public function testErrno505()
    {
      $request = Request::createFromGlobals();
      $request->query->set('ERRNO', '500');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(500, $response->getStatusCode());
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
    }

    public function testIndex()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
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
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'GNU/Linux Inside!'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Can you delete my key from the key server?'));
      $this->assertSame(FALSE, strpos($response->getContent(), 'Remove my key!'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Please send bug reports'));
    }

    public function testMissing()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/doc/missing');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertSame(0, strpos($response->getContent(), '<!DOCTYPE html>'));
    }
}
