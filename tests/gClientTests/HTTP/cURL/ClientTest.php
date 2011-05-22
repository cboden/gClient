<?php
namespace gClientTests\HTTP\cURL;
use gClient\HTTP\cURL;

/**
 * @covers \gClient\HTTP\cURL\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase {
    protected $_client;

    public function setUp() {
        $this->_client = new cURL\Client('http://localhost');
    }

    public function testClientInterface() {
        $this->assertInstanceOf('\gClient\HTTP\ClientInterface', $this->_client);
    }

    public function testInvalidUrlException() {
        $this->setExpectedException('\InvalidArgumentException');
        new cURL\Client('invalid url');
    }

    /**
     * @covers \gClient\HTTP\cURL\Client::method
     */
    public function testMethodSuccess() {
        try {
            $this->_client->method('GET');
            $this->_client->method('POST');
            $this->_client->method('PUT');
            $this->_client->method('DELETE');
        } catch (\Exception $e) {
            $this->fail("Client::method threw an exception on GET|POST|PUT|DELETE ({$e->getMessage()})");
        }
    }

    /**
     * @depends testMethodSuccess
     */
    public function testVerifyMethodSet() {
        $this->_client->method('POST');
        $this->assertAttributeEquals('POST', 'method', $this->_client);
    }

    /**
     * @covers \gClient\HTTP\cURL\Client::setRawData
     */
    public function testSetRawData() {
        $string = 'Hello World!';

        $this->_client->setRawData($string);
        $this->assertAttributeEquals($string, 'params', $this->_client);
    }

    public function testSetRawDataWithArray() {
        $array  = Array('data' => Array('Hello' => 'World'));
        $string = json_encode($array);

        $this->_client->setRawData($array);
        $this->assertAttributeEquals($string, 'params', $this->_client);
    }

    /**
     * @covers \gClient\HTTP\cURL\Client::setParameter
     */
    public function testSetParameter() {
        list($key, $val) = Array('Hello', 'World');

        $this->_client->setParameter($key, $val);
        $this->assertAttributeEquals(Array($key => $val), 'params', $this->_client);
    }

    /**
     * @depends testVerifyMethodSet
     */
    public function testInvalidMethod() {
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