<?php
namespace gClientTests;
use gClient\Connection;

/**
 * @covers \gClient\Connection
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase {
    protected $_conn;

    public function setUp() {
        $this->_conn = new Connection();
    }

    /**
     * @covers \gClient\Connection::addService
     */
    public function testAddServiceCalendar() {
        $this->_conn->addService('Calendar');
        $this->assertAttributeEquals(Array('Calendar' => 'gClient\Calendar\Service'), 'services', $this->_conn);
    }

    public function testAddServiceMock() {
        $this->_conn->addService('\gClientTests\Mocks\ServiceMock');
        $this->assertAttributeEquals(Array('\gClientTests\Mocks\ServiceMock' => '\gClientTests\Mocks\ServiceMock'), 'services', $this->_conn);
    }

    /**
     * @ covers \gClient\Connection::getService
     * @ depends testAddServiceCalendar
     * @todo HttpMocks need to be done with fake data before this can be done
     * /
    public function testGetServiceCalendar() {
        $this->_conn->addService('Calendar');
    }
*/

    /**
     * @covers \gClient\Connection::isAuthenticated
     */
    public function testIsConnectedSuccess() {
        $this->assertTrue($this->_conn->isAuthenticated());
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