<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use gClient\HTTP;
use SplDoublyLinkedList, Closure;

// maybe to list generation on construct
// Catalog, Listing, Library, Directory
// Manager, Cabnet, Composite, ???

// new SplPriorityQueue

/**
 * This is the main Calendar controlling class
 * It's class name is subject to change before 1.0
 * 
 * @property Settings $settings A settings object of the Adapter users' Google Calendar Settings
 * @link http://code.google.com/apis/calendar/data/2.0/developers_guide_protocol.html Calendar API Protocol documentation
 * @link http://code.google.com/apis/calendar/data/2.0/reference.html Calendar property reference
 */
class Catalog implements \SeekableIterator, \Countable {
    /**
     * @internal
     */
    protected $readonly = Array();

    const CLIENTLOGIN_SERVICE = 'cl';
    const OAUTH_SCOPE         = 'https://www.google.com/calendar/feeds/';

    const ALL_LIST_URL   = '/calendar/feeds/default/allcalendars/full';
    const OWNER_LIST_URL = '/calendar/feeds/default/owncalendars/full';

    const SETTINGS_URL = '/calendar/feeds/default/settings';

    /**
     * Adapter to use to make HTTP calls with
     * @var \gClient\Auth\Adapter
     */
    protected $adapter;

    /**
     * Calendar data list returned from Google
     * @var Array
     */
    protected $data;

    /**
     * Array of Calendar objects (cache)
     * @var Array
     */
    protected $calendars = Array();

    /**
     * Internal pointer for looping through Calendar objects
     * @var int
     */
    protected $pos = 0;

    /**
     * @param \gClient\Auth\Adapter
     * @param boolean TRUE to list only the calendars owned/created by user | FALSE to list all calendars in users' list
     */
    public function __construct(Adapter $adapter, $only_owner = false) {
        $this->adapter = $adapter;

        $response = $adapter->reqFactory(((boolean)$only_owner ? static::OWNER_LIST_URL : static::ALL_LIST_URL))->method('GET')->request();

        if ($response->getStatusCode() != 200) {
            throw new HTTP\Exception($response);
        }

        $data = json_decode($response->getContent(), true);
        $this->data = $data['data'];
    }

    public function &__get($name) {
        if ($name == 'settings') {
            $this->fetchSettings();
        }

        if (!isset($this->readonly[$name])) {
            $this->readonly[$name] = '';
        }

        return $this->readonly[$name];
    }

    /**
     * @param string $name Name of calendar to create
     * @param Array Additional configuration array of data for creating the calendar
     * @return Calendar
     * @throws \UnexpectedValueException On an empty name/title or invalid color
     * @throws \gClient\HTTP\Exception On a bad return from Google
     */
    public function createCalendar($name = null, Array $attributes = Array()) {
        // I'm still on the fence of which wasy this should be...currently $attributes['title'] wins...
        $content = $attributes + Array('title' => $name);

        if (is_null($content['title']) || empty($content['title'])) {
            throw new \UnexpectedValueException('Calendar name/title should be set');
        }

        if (isset($content['color']) && !in_array($content['color'], Calendar::$valid_colors)) {
            throw new \UnexpectedValueException("{$content['color']} is not a valid calendar color");
        }

        $adapter = $this->adapter;
        $res     = $adapter->reqFactory(static::OWNER_LIST_URL)->method('POST')->setRawData(Array('data' => $content))->request();
        if (201 != ($http_code = $res->getStatusCode())) {
            throw new HTTP\Exception($res);
        }

        $data = json_decode($res->getContent(), true);
        $this->data[] = $data['data'];

        end($this->data);
        $key = key($this->data);

        $this->calendars[$key] = new Calendar($data['data'], $adapter);
        return $this->calendars[$key];
    }

    /**
     * @param Calendar|string ID of the calendar to delete
     * @return boolean
     * @throws \gClient\HTTP\Exception
     */
    public function deleteCalendar($calendar) {
        $url = $calendar;
        if ($calendar instanceof Calendar) {
            $url = $calendar->selfLink;
        }

        $res = $this->adapter->reqFactory($url)->method('DELETE')->request();
        if ($res->getStatusCode() != 200) {
            throw new HTTP\Exception($res);
        }

        // remove internal pointers

        return true;
    }

    /**
     * @param string ID-URL of calendar to subscribe to
     * @return Calendar
     */
    public function subscribeToCalendar($id_url) {
    }

    public function unsubscribeFromCalendar() {
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->data['items']);
    }

    /**
     * @param int|string
     */
    public function seek($position) {
        $this->pos = $pos;

        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid index');
        }

        return $this->current();
    }

    /**
     * @return Calendar
     */
    public function current() {
        if (!isset($this->calendars[$this->pos])) {
            $this->calendars[$this->pos] = new Calendar($this->data['items'][$this->pos], $this->adapter);
        }

        return $this->calendars[$this->pos];
    }

    /**
     * @return int
     */
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
     * @return bool
     */
    public function valid() {
        return isset($this->data['items'][$this->pos]);
    }

    protected function fetchSettings() {
        if (isset($this->readonly['settings'])) {
            return;
        }

        $this->readonly['settings'] = new Settings($this->adapter->reqFactory(static::SETTINGS_URL)->request());
    }
}