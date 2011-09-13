<?php
namespace gClient\Tests\Calendar\Builder;
use gClient\Calendar\Builder\NewCalendar;

/**
 * @covers gClient\Calendar\Builder\NewCalendar
 */
class NewCalendarTest extends \PHPUnit_Framework_TestCase {
    protected $_nc;
    protected $_title = 'New Calendar Test';

    public function setUp() {
        $this->_nc = new NewCalendar($this->_title);
    }

    public function testConstructSetsTitle() {
        $this->assertEquals($this->_title, $this->_nc->params['title']);
    }

    public function testGetRequiredFieldsReturnsSplArray() {
        $nc = $this->_nc;
        $this->assertInstanceOf('SplFixedArray', $nc::getRequiredFields());
    }

    public function testGetOptionalFieldsReturnsSplArray() {
        $nc = $this->_nc;
        $this->assertInstanceOf('SplFixedArray', $nc::getOptionalFields());
    }

    public function testSetDetailMethodSetsDetails() {
        $d = 'details string';
        $this->_nc->setDetails($d);

        $this->assertEquals($d, $this->_nc->params['details']);
    }

    public function testSetTimeZoneMethodSetsTimezone() {
        $string = 'America/Los_Angeles';
        $dtz    = new \DateTimeZone($string);
        $this->_nc->setTimeZone($dtz);

        $this->assertEquals($string, $this->_nc->params['timezone']);
    }

    public function testSetHiddenMethodSetsHidden() {
        $hidden = true;
        $this->_nc->setHidden($hidden);

        $this->assertEquals($hidden, $this->_nc->params['hidden']);
    }

    public function testSetColorMethodSetsColor() {
        $colour = '#705770';
        $this->_nc->setColor($colour);

        $this->assertEquals($colour, $this->_nc->params['color']);
    }

    public function testSetLocationMethodSetsLocation() {
        $location = 'London';
        $this->_nc->setLocation($location);

        $this->assertEquals($location, $this->_nc->params['location']);
    }

    public function testColourAliasesColor() {
        $colour = '#705770';
        $this->_nc->setColour($colour);

        $this->assertEquals($colour, $this->_nc->params['color']);
    }

    public function testFluentInterface() {
        $this->assertSame($this->_nc, $this->_nc->setDetails('detail string'));
        $this->assertSame($this->_nc, $this->_nc->setTimeZone(new \DateTimeZone('America/Los_Angeles')));
        $this->assertSame($this->_nc, $this->_nc->setHidden(false));
        $this->assertSame($this->_nc, $this->_nc->setColor('#705770'));
        $this->assertSame($this->_nc, $this->_nc->setColour('#705770'));
        $this->assertSame($this->_nc, $this->_nc->setLocation('London'));
    }
}