<?php
namespace gClientTests\HTTP\cURL;
use gClient\HTTP\cURL;

class ClientTest extends \PHPUnit_Framework_TestCase {
    protected $_client;

    public function setUp() {
        $this->_client = new cURL\Client('http://localhost');
    }

    public function testInterface() {
        $this->assertType('\gClient\HTTP\ClientInterface', $this->_client);
    }

    public function testInvalidURL() {
        $this->setExpectedException('\InvalidArgumentException');
        new cURL\Client('invalid url');
    }

    public function testMethods() {
        try {
            $this->_client->method('GET');
            $this->_client->method('POST');
            $this->_client->method('PUT');
            $this->_client->method('DELETE');
        } catch (\Exception $e) {
            $this->fail("Client::method threw an exception on GET|POST|PUT|DELETE ({$e->getMessage()})");
        }

        $this->setExpectedException('\Exception');
        $this->_client->method('invalid method');
    }

    public function testFluentInterface() {
        $this->assertSame($this->_client, $this->_client->method('GET'));
        $this->assertSame($this->_client, $this->_client->setRawData('a'));
        $this->assertSame($this->_client, $this->_client->setParameter('a', 'b'));
        $this->assertSame($this->_client, $this->_client->addHeader('a'));
        $this->assertSame($this->_client, $this->_client->setParameters(Array('a' => 'b')));
        $this->assertSame($this->_client, $this->_client->addHeaders(Array('a')));
    }
}