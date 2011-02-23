<?php
namespace cB\gData\Calendar;
use cB\Common, cB\Common\TypeCast, cB\gData\Auth;
use SplDoublyLinkedList, Closure;

const ALL_LIST_URL   = 'https://www.google.com/calendar/feeds/default/allcalendars/full';
const OWNER_LIST_URL = 'https://www.google.com/calendar/feeds/default/owncalendars/full';

// maybe to list generation on construct
// iteratable/countable instance
// Catalog, Listing, Library, Directory
// Manager, Cabnet, Composite, ???

// new SplPriorityQueue

class Catalog {
    protected $clendars = Array();
    protected $lookup   = Array();

    protected $adapter;
    protected $only_owner = false;

    public function __construct(Auth\Adapter $adapter, $only_owner = false) {
        $this->adapter = $adapter;

        $response = $this->adapter->request(new TypeCast\URL((boolean)$only_owner ? OWNER_LIST_URL : ALL_LIST_URL), 'GET');
        $this->calendars = $response['feed']['entry'];
    }

    public function each(Closure $fn) {
        array_map($fn, $this->calendars);
        return $this;
    }

    /**
     * @deprecated soon
     */
    public function getCalendars() {
        $return = Array();
        foreach ($this->calendars as $key => $data) {
            // ['content']['src'] . . . 

            $lookup[$data['id']['$t']] = $key;
            $return[$data['title']['$t']] = $data['id']['$t'];
        }

        return $return;
    }

    public function createCalendar($name) {
    }

    public function deleteCalendar() {
    }
}
?>