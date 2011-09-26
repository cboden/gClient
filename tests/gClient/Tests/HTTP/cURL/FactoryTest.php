<?php
namespace gClient\Tests\HTTP\cURL;
use gClient\HTTP\cURL;

/**
 * @covers \gClient\HTTP\cURL\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase {
    protected $_factory;

    public function setUp() {
        $this->_factory = new cURL\Factory();
    }

    public function testMakeClientReturnsClientInterface() {
        $this->assertInstanceOf('\\gClient\\HTTP\\ClientInterface', $this->_factory->makeClient('http://localhost'));
    }

/*
    public function testMakeResponseReturnsResponseInterface() {
        $this->assertInstanceOf('\\gClient\\HTTP\\ResponseInterface', $this->_factory->makeResponse(
    }
/**/
}