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

    public function testNewCalendarBuilderFactoryReturnType() {
        $builder = $this->_service->buildCalendar('Hello World!');

        $this->assertInstanceOf('\\gClient\\Calendar\\Builder\\NewCalendar', $builder);
    }

    public function testNewCalendarBuilderFactoryValue() {
        $title   = 'This is the title';
        $builder = $this->_service->buildCalendar($title);

        $this->assertEquals($title, $builder->params['title']);
    }

    public function testIteratorInterface() {
        $this->assertInstanceOf('\\IteratorAggregate', $this->_service);
    }

    public function testIteratorInterfaceReturnsIterator() {
        $this->markTestIncomplete('getIterator is trying to connect to Google...need to intercept it to complete test');
        return;

        $this->assertInstanceOf('\\Iterator', $this->_service->getIterator());
    }
}