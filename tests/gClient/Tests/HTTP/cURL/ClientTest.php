<?php
namespace gClient\Tests\HTTP\cURL;
use gClient\HTTP\cURL;

/**
 * @covers \gClient\HTTP\cURL\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase {
    protected $_factory;
    protected $_client;

    public function setUp() {
        $this->_factory = new cURL\Factory();
        $this->_client  = $this->_factory->makeClient('http://localhost');
    }

    public function testClientInterface() {
        $this->assertInstanceOf('\gClient\HTTP\ClientInterface', $this->_client);
    }

    public function testInvalidUrlException() {
        $this->setExpectedException('\InvalidArgumentException');
        $this->_factory->makeClient('invalid url');
    }

    public function testMethodSuccess() {
        try {
            $this->_client->setMethod('GET');
            $this->_client->setMethod('POST');
            $this->_client->setMethod('PUT');
            $this->_client->setMethod('DELETE');
        } catch (\Exception $e) {
            $this->fail("Client::method threw an exception on GET|POST|PUT|DELETE ({$e->getMessage()})");
        }
    }

    /**
     * @depends testMethodSuccess
     */
    public function testVerifyMethodSet() {
        $this->_client->setMethod('POST');
        $this->assertAttributeEquals('POST', 'method', $this->_client);
    }

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

    public function testSetParameter() {
        list($key, $val) = Array('Hello', 'World');

        $this->_client->setParameter($key, $val);
        $this->assertAttributeEquals(Array($key => $val), 'params', $this->_client);
    }

    /**
     * @depends testVerifyMethodSet
     */
    public function testInvalidMethod() {
        $this->setExpectedException('\InvalidArgumentException');
        $this->_client->setMethod('invalid method');
    }

    public function testFluentInterface() {
        $this->assertSame($this->_client, $this->_client->setMethod('GET'));
        $this->assertSame($this->_client, $this->_client->setRawData('a'));
        $this->assertSame($this->_client, $this->_client->setParameter('a', 'b'));
        $this->assertSame($this->_client, $this->_client->addHeader('a'));
        $this->assertSame($this->_client, $this->_client->setParameters(Array('a' => 'b')));
        $this->assertSame($this->_client, $this->_client->addHeaders(Array('a')));
    }
}