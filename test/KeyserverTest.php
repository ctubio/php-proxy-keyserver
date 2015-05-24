<?php
use PhpProxy\Keyserver;
use PhpProxy\Keyserver\Config;
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
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
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
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
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
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
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
      $this->assertStringStartsWith('User-agent: *', $response->getContent());
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

    public function testConfig()
    {
      $config = Keyserver::getConfig();

      $this->assertTrue($config instanceof Config);
      $this->assertGreaterThan(1, $config->hkp_port);
      $this->assertGreaterThan(1, strlen($config->html_skin));
    }

    public function testStats()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/pks/lookup?op=stats');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
      $this->assertGreaterThan(21, strpos($response->getContent(), 'OpenPGP Keyserver statistics'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Total number of keys:'));
    }

    public function testShort()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/pks/lookup?search=x&fingerprint=on&op=vindex');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(500, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
      $this->assertGreaterThan(21, strpos($response->getContent(), 'keyword too short..'));
    }

    public function testNotfound()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/pks/lookup?search=IMPOSSIBLEKEYTS&fingerprint=on&op=vindex');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
      $this->assertGreaterThan(21, strpos($response->getContent(), '0 keys found..'));
    }

    public function test0xC3B39DE0()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/pks/lookup?search=0xC3B39DE0&fingerprint=on&op=vindex');
      Keyserver::$request_instance = $request;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Search results for \'0xc3b39de0\''));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'Carles Tubio (pgp.key-server.io)'));
      $this->assertGreaterThan(21, strpos($response->getContent(), '0xFA101D1FC3B39DE0'));
    }

    public function testUnreachable()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/pks/lookup?op=stats');
      Keyserver::$request_instance = $request;
      Keyserver::getConfig()->hkp_addr = 'bad.domain.tld';
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
      $this->assertGreaterThan(21, strpos($response->getContent(), '<pre>Hint! Double-check'));
    }

    public function testSilentUnreachable()
    {
      $request = Request::createFromGlobals();
      $request->server->set('REQUEST_URI', '/pks/lookup?op=stats');
      Keyserver::$request_instance = $request;
      Keyserver::getConfig()->display_exceptions = FALSE;
      $response = Keyserver::getResponse();

      $this->assertTrue($response instanceof Response);
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('text/html;charset=UTF-8', $response->headers->get('content-type'));
      $this->assertStringStartsWith('<!DOCTYPE html>', $response->getContent());
      $this->assertStringEndsWith('</html>'.PHP_EOL, $response->getContent());
      $this->assertSame(FALSE, strpos($response->getContent(), '<pre>Hint! Double-check'));
      $this->assertGreaterThan(21, strpos($response->getContent(), 'An error ocurred. Please'));
    }
}
