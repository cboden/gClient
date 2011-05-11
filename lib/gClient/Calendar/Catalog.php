<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use SplDoublyLinkedList, Closure;

const ALL_LIST_URL   = 'https://www.google.com/calendar/feeds/default/allcalendars/full';
const OWNER_LIST_URL = 'https://www.google.com/calendar/feeds/default/owncalendars/full';

// maybe to list generation on construct
// iteratable/countable instance
// Catalog, Listing, Library, Directory
// Manager, Cabnet, Composite, ???

// new SplPriorityQueue

class Catalog {
    protected $adapter;
    protected $data;

    public function __construct(Adapter $adapter, $only_owner = false) {
        $this->adapter = $adapter;

        $response = $this->adapter->reqFactory((boolean)$only_owner ? OWNER_LIST_URL : ALL_LIST_URL)->method('GET')->request();
        $data = json_decode($response->getContent(), true);
        $this->data = $data['data'];
    }

    /**
     * @deprecated soon
     */
    public function getCalendars() {
        return $this->data['items'];
    }

    public function createCalendar($name) {
    }

    public function deleteCalendar() {
    }
}