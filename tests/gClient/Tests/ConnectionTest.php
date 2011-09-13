<?php
namespace gClient\Tests;

/**
 * @covers gClient\Connection
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase {
    protected static $_class = '\\gClient\\Connection';
    protected $_conn;

    public function setUp() {
        $class       = static::$_class;
        $this->_conn = new $class();
    }

    protected static function getMethod($name) {
        $class = new \ReflectionClass(static::$_class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testAddServiceCalendar() {
        $this->_conn->addService('Calendar');
        $this->assertAttributeEquals(Array('Calendar' => 'gClient\Calendar\Service'), 'services', $this->_conn);
    }

    public function testAddServiceMock() {
        $this->_conn->addService('\gClient\Tests\Mock\Service');
        $this->assertAttributeEquals(Array('\gClient\Tests\Mock\Service' => '\gClient\Tests\Mock\Service'), 'services', $this->_conn);
    }

    /**
     * @ depends testAddServiceCalendar
     * @todo HttpMocks need to be done with fake data before this can be done
     * /
    public function testGetServiceCalendar() {
        $this->_conn->addService('Calendar');
    }
*/

    public function testIsConnectedSuccess() {
        $this->assertTrue($this->_conn->isAuthenticated());
    }

    public function testCanSerializeConnection() {
        $serialized  = serialize($this->_conn);
        $objectified = unserialize($serialized);

        $constraint = $this->isInstanceOf('\\gClient\\Connection');
        $this->assertThat($objectified, $constraint);
    }

    public function testGetAnInvalidServiceClassException() {
        $this->setExpectedException('\\RuntimeException');

        $method = $this->getMethod('getServiceClass');
        $method->invokeArgs($this->_conn, Array('HerpDerpFudgeBananas'));
    }

    public function testVerifyServiceClassIsImplementsService() {
        $this->setExpectedException('\\UnexpectedValueException');

        $method = $this->getMethod('getServiceClass');
        $method->invokeArgs($this->_conn, Array(new \DateTime('now'), true));
    }

    public function testVerifyPublicClientClassEnforced() {
        $this->setExpectedException('\\RuntimeException');

        $this->_conn->req_class = '\\StdClass';
        $this->_conn->prepareCall('http://localhost');
    }

    public function testAuthenticateReturnsSelf() {
        $this->assertSame($this->_conn, $this->_conn->authenticate());
    }

/* trying to test isAuthenticated without __construct() - still learning Mock, not there yet
    public function testIsConnectedFalse() {
        $mock_conn = $this->getMock(
            '\gClient\Connection'
          , Array('isAuthenticated')
          , Array()
          , ''
          , false
        );

        $this->assertFalse($mock_conn->isAuthenticated());
    }
*/
}