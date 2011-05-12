<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use SplDoublyLinkedList, Closure;

const ALL_LIST_URL   = 'https://www.google.com/calendar/feeds/default/allcalendars/full';
const OWNER_LIST_URL = 'https://www.google.com/calendar/feeds/default/owncalendars/full';

// maybe to list generation on construct
// Catalog, Listing, Library, Directory
// Manager, Cabnet, Composite, ???

// new SplPriorityQueue

class Catalog implements \SeekableIterator, \Countable {
    protected $adapter;
    protected $data;

    protected $calendars = Array();
    protected $pos       = 0;

    public function __construct(Adapter $adapter, $only_owner = false) {
        $this->adapter = $adapter;

        $response = $this->adapter->reqFactory((boolean)$only_owner ? OWNER_LIST_URL : ALL_LIST_URL)->method('GET')->request();
        $data = json_decode($response->getContent(), true);
        $this->data = $data['data'];
    }

    public function createCalendar($name) {
    }

    public function deleteCalendar() {
    }

    public function subscribeToCalendar() {
    }

    public function unsubscribeFromCalendar() {
    }

    /**
     * @returns int
     */
    public function count() {
        return count($this->data['items']);
    }

    /**
     * @param mixed(int|url-id) $position
     */
    public function seek($position) {
        $this->pos = $pos;

        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid index');
        }

        return $this->current();
    }

    /**
     * @returns \gClient\Calendar\Calendar
     */
    public function current() {
        if (!isset($this->calendars[$this->pos])) {
            $this->calendars[$this->pos] = new Calendar($this->data['items'][$this->pos], $this->adapter);
        }

        return $this->calendars[$this->pos];
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        $this->pos++;
    }

    public function rewind() {
        $this->pos = 0;
    }

    /**
     * @returns bool
     */
    public function valid() {
        return isset($this->data['items'][$this->pos]);
    }
}