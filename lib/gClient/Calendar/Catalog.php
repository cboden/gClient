<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use SplDoublyLinkedList, Closure;

const ALL_LIST_URL   = '/calendar/feeds/default/allcalendars/full';
const OWNER_LIST_URL = '/calendar/feeds/default/owncalendars/full';

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

        $response = $adapter->reqFactory($adapter::BASE_URL . ((boolean)$only_owner ? OWNER_LIST_URL : ALL_LIST_URL))->method('GET')->request();
        $data = json_decode($response->getContent(), true);
        $this->data = $data['data'];
    }

    public function createCalendar($name, Array $attributes = Array()) {
        $content = json_encode(Array('data' => Array(
            'title'    => $name
          , 'details'  => 'description goes here'
          , 'timeZone' => 'America/Toronto'
          , 'hidden'   => false
          , 'color'    => '#2952A3'
          , 'location' => 'London'
        )));

        $adapter = $this->adapter;
        $res     = $adapter->reqFactory($adapter::BASE_URL . OWNER_LIST_URL)->method('POST')->setRawData($content)->request();
        $data    = json_decode($res->getContent(), true);
        $this->data[] = $data['data'];

        end($this->data);
        $key = key($this->data);

        $this->calendars[$key] = new Calendar($data['data'], $adapter);
        return $this->calendars[$key];
    }

    public function deleteCalendar($id) {
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