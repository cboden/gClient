<?php
namespace gClient\Tests\Calendar;
use gClient\Connection;
use gClient\Calendar\Service;

/**
 * @covers gClient\Calendar\Service
 */
class ServiceTest extends \PHPUnit_Framework_TestCase {
    protected $_service;

    public function setUp() {
        $this->_service = new Service(new Connection());
    }

    public function testServiceClassImplementsServiceInterface() {
        $this->assertInstanceOf('\\gClient\\ServiceInterface', $this->_service);
    }

    public function testOAuthScopeIsAValidUrl() {
        $url = Service::getOAuthScope();
        $this->assertTrue((boolean)filter_var($url, FILTER_VALIDATE_URL));
    }

    public function testClientLoginServiceIsString() {
        $this->assertInternalType('string', Service::getClientLoginService());
    }

    public function testCanSerialize() {
        $serialized  = serialize($this->_service);
        $objectified = unserialize($serialized);

        $this->assertInstanceOf('\\gClient\\Calendar\\Service', $objectified);
    }
}