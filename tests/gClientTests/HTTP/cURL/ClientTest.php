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
        $this->assertEquals('POST', $this->readAttribute($this->_client, 'method'));
    }

    /**
     * @covers \gClient\
     * @dataProvider providerRawData
     */
    public function testSetRawData($string) {
        $this->_client->setRawData($string);
        if (is_array($string)) {
            $string = json_encode($string);
        }
        $this->assertEquals($string, $this->readAttribute($this->_client, 'params'));
    }

    public function providerRawData() {
        return Array(
            Array('Hello World!')
          , Array(Array('Hello' => 'World'))
        );
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