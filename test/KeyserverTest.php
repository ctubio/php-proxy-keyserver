<?php
use PhpProxy\Keyserver;
use Symfony\Component\HttpFoundation\Response;

class KeyserverTest extends PHPUnit_Framework_TestCase
{
    public function testWtf()
    {
        $response = Keyserver::getResponse();
        $this->assertTrue($response instanceof Response);
    }
}
