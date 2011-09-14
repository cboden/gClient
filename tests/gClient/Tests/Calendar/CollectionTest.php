<?php
namespace gClient\Tests\Calendar;
//use gClient\Tests\Mocks\ServiceMock;
use gClient\Connection;
use gClient\Calendar\Service;
use gClient\Calendar\Service\Collection;
use gClient\Calendar\Calendar as Cal;

/**
 * @covers gClient\Calendar\Service\Collection
 * @notes - because I designed to a concrete I can't switch Service with MockService...todo: fix that
 */
class CollectionTest extends \PHPUnit_Framework_TestCase {
    protected $_conn;
    protected $_serv;
    protected $_collection;

    public function setUp() {
        $this->_conn       = new Connection();
        $this->_serv       = new Service($this->_conn);
        $this->_collection = new Collection();
    }

    public function testInsertAndCount() {
        $cals = $this->fillProvider();
        $this->assertEquals(count($cals), $this->_collection->count());
    }

    public function testIteratorAndOrder() {
        $this->fillProvider();
        $base = $this->calendarProvider();

        foreach ($this->_collection as $o => $cal) {
            $ver = $base[$o];
            $this->assertEquals($cal->properties->id, $ver->properties->id);
        }
    }

    public function testSeekAndKeyAndCurrent() {
        $this->fillProvider();
        $cals = $this->calendarProvider();

        $this->assertEquals(0, $this->_collection->key());

        $new_pos = 2;
        $this->_collection->seek($new_pos);
        $this->assertEquals($new_pos, $this->_collection->key());

        $cal = $this->_collection->current();
        $this->assertEquals($cals[$new_pos]->properties->id, $cal->properties->id);
    }

    public function testSeekByUniqueId() {
        $rand = $this->fillProvider();
        $cals = $this->calendarProvider();

        $pos = 3;
        $this->_collection->seek($cals[3]->unique_id);
        $this->assertEquals($pos, $this->_collection->key());
    }

    public function testInvalidSeek() {
        $this->fillProvider();

        $this->setExpectedException('OutOfBoundsException');
        $this->_collection->seek($this->_collection->count() + 5);
    }

    public function testInvalidSeekByUniqueId() {
        $this->fillProvider();

        $this->setExpectedException('OutOfBoundsException');
        $this->_collection->seek('not_a_real_id');
    }

    public function testRemoveItem() {
        $cals = $this->fillProvider();
        $this->_collection->remove($cals[array_rand($cals)]);
        $this->assertEquals((count($cals) - 1), $this->_collection->count());
    }

    public function testRemoveNonExistentItem() {
        $cals = $this->fillProvider();

        $this->setExpectedException('OutOfBoundsException');
        $this->_collection->remove($this->_collection->count() + 5);
    }

    public function testIterationPointerAfterRemovingItem() {
        $cals = $this->fillProvider();
        $base = $this->calendarProvider();
        $pos  = 2;

        $this->_collection->seek($pos);
        $this->_collection->remove($base[$pos]->unique_id);

        $current = $this->_collection->current();
        $this->assertEquals($current->unique_id, $base[$pos - 1]->unique_id);
    }

    public function fillProvider() {
        $cals = $this->calendarProvider();
        shuffle($cals);

        foreach ($cals as $cal) {
            $this->_collection->insert($cal);
        }

        return $cals;
    }

    public function calendarProvider() {
        return Array(
            // Test entries here should be in order in that of Google's
            new Cal(Array('accessLevel' => 'owner',     'id' => 'google/something', 'title' => 'A'), $this->_serv)
          , new Cal(Array('accessLevel' => 'read',      'id' => 'google/place',     'title' => 'B'), $this->_serv)
          , new Cal(Array('accessLevel' => 'read',      'id' => 'google/lower',     'title' => 'C'), $this->_serv)
          , new Cal(Array('accessLevel' => 'freebusy',  'id' => 'google/hello',     'title' => 'D'), $this->_serv)
          , new Cal(Array('accessLevel' => 'editor',    'id' => 'google/world',     'title' => 'E'), $this->_serv)
        );
    }
}