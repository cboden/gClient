<?php
namespace gClientTests;
use gClient\PropertyProxy;

/**
 * @covers \gClient\PropertyProxy
 * /
class PropertyProxyTest extends \PHPUnit_Framework_TestCase {
    protected $_proxy;

    public function setUp() {
        $this->_proxy = new PropertyProxy();
    }

    // I'm having difficult managing scope/reflection
    public function testSetterOverLoad() {
        $class = new \ReflectionClass('\gClient\PropertyProxy');
        $method = $class->getMethod('setData');

//        $method = new \ReflectionMethod('\gClient\PropertyProxy', 'setData');
        $method->setAccessible(true);

        $method->invoke(Array('key' => 'one'));
        $method->invoke(Array('key' => 'two'));

//        $rovar = $this->readAttribute($this->_client, 'readonly');
//        $this->assertEquals(Array('key' => 'two'), $rovar);

        $this->assertEquals($class->key, 'two');
    }
}
/**/