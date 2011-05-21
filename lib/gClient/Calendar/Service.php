<?php
namespace gClient\Calendar;
use gClient\Connection;
use gClient\HTTP;
use SplDoublyLinkedList, Closure;

// maybe to list generation on construct
// Catalog, Listing, Library, Directory
// Manager, Cabnet, Composite, ???

// new SplPriorityQueue

/**
 * This is the main Calendar controlling class
 * It's class name is subject to change before 1.0 - I'm not stuck on it being an Iterator (::getCalendars() instead maybe)
 * 
 * @property Settings $settings A settings object of the Connection users' Google Calendar Settings
 * @link http://code.google.com/apis/calendar/data/2.0/developers_guide_protocol.html Calendar API Protocol documentation
 * @link http://code.google.com/apis/calendar/data/2.0/reference.html Calendar property reference
 */
class Service implements \gClient\ServiceInterface, \SeekableIterator, \Countable {
    /**
     * @internal
     */
    protected $readonly = Array();

    const CLIENTLOGIN_SERVICE = 'cl';
    const OAUTH_SCOPE         = 'https://www.google.com/calendar/feeds/';

    const PROTOCOL_VERSION = 'GData-Version: 2';
    const CONTENT_TYPE     = 'application/json';
    const ALT              = 'jsonc';

    const ALL_LIST_URL   = '/calendar/feeds/default/allcalendars/full/';
    const OWNER_LIST_URL = '/calendar/feeds/default/owncalendars/full/';

    const SETTINGS_URL = '/calendar/feeds/default/settings/';

    /**
     * Connection to use to make HTTP calls with
     * @var \gClient\Connection
     */
    protected $connection;

    /**
     * Calendar data list returned from Google
     * @var Array
     */
    protected $data = Array();

    /**
     * Array of Calendar objects (cache)
     * @var Array
     */
    protected $calendars = Array();

    /**
     * @internal
     */
    protected $lookup = Array();

    /**
     * Internal pointer for looping through Calendar objects
     * @var int
     */
    protected $pos = 0;

    /**
     * @param \gClient\Connection gData connection to make API calls through
     */
    public function __construct(Connection $connection) {
        $only_owner = false;
        $this->connection = $connection;

        $response = $this->prepareCall(((boolean)$only_owner ? static::OWNER_LIST_URL : static::ALL_LIST_URL))->method('GET')->request();
        $data = json_decode($response->getContent(), true);

        foreach ($data['data']['items'] as $i => $caldata) {
            $calendar = new Calendar($caldata, $connection);

            $this->calendars[$calendar->unique_id] = $calendar;
            $this->lookup[] = $calendar->unique_id;
        }

        unset($data['data']['items']);
        $this->data = $data['data'];
    }

    /**
     * @param string $name Name of calendar to create
     * @param Array Additional creational params - associative keys: (details, timeZone, hidden, color, location)
     * @return Calendar
     * @throws \UnexpectedValueException On an empty name/title or invalid color
     * @throws \gClient\HTTP\Exception On a bad return from Google
     * @todo This appends to end of all calendars - should append to end of owner calendars - including alphabetical order
     * @todo Consider setting timeZone from Settings if not set in $attributes
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

        $res = $this->prepareCall(static::OWNER_LIST_URL)->method('POST')->setRawData(Array('data' => $content))->request();
        if (201 != ($http_code = $res->getStatusCode())) {
            throw new HTTP\Exception($res);
        }

        $data = json_decode($res->getContent(), true);
        return $this->appendCalendar($data['data']);
    }

    /**
     * @param Calendar|string The Calendar instance, selfLink or unique_id of the calendar to delete
     * @return Service $this
     * @throws \gClient\HTTP\Exception
     */
    public function deleteCalendar($calendar) {
        $url = $calendar;
        if ($calendar instanceof Calendar) {
            $url = $calendar->selfLink;
        }

        $own_url = str_replace(static::ALL_LIST_URL, static::OWNER_LIST_URL, $url);

        if (!(boolean)filter_var($own_url, FILTER_VALIDATE_URL)) {
            $own_url = static::OWNER_LIST_URL . $url;
        }

        $res = $this->prepareCall($own_url)->method('DELETE')->request();
        if ($res->getStatusCode() != 200) {
            throw new HTTP\Exception($res);
        }

        $uid = substr($own_url, strrpos($own_url, '/') + 1);

        unset($this->calendars[$uid]);
        array_splice($this->lookup, array_search($uid, $this->lookup), 1);

        return $this;
    }

    /**
     * Subscribe to a calendar
     * @param string ID of calendar to subscribe to
     * @return Calendar
     */
    public function subscribeToCalendar($id) {
        $res = $this->prepareCall(static::ALL_LIST_URL)->method('POST')->setRawData(Array('data' => Array('id' => $id)))->request();

        $data = json_decode($res->getContent(), true);
        return $this->appendCalendar($data['data']);
    }

    /**
     * @param \gData\Calendar\Calendar|string The instance of Calendar to delete or the unique_id of the calendar
     * @throws \gClient\HTTP\Exception
     * @return Service $this
     */
    public function unsubscribeFromCalendar($calendar) {
        $url = $calendar;
        if ($calendar instanceof Calendar) {
            $url = static::ALL_LIST_URL . $calendar->unique_id;
        }

        if (!(boolean)filter_var($url, FILTER_VALIDATE_URL)) {
            $url = static::ALL_LIST_URL . $url;
        }

        $this->prepareCall($url)->method('DELETE')->request();

        $uid = substr($url, strrpos($url, '/') + 1);

        if (isset($this->calendars[$uid])) {
            unset($this->calendars[$uid]);
            array_splice($this->lookup, array_search($uid, $this->lookup), 1);
        }

        return $this;
    }

    /**
     * @param Array
     * @return Calendar
     */
    protected function appendCalendar($data) {
        $calendar = new Calendar($data, $this->connection);

        $this->calendars[$calendar->unique_id] = $calendar;
        $this->lookup[] = $calendar;

        return $calendar;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->calendars);
    }

    /**
     * @param int|string
     * @todo allow $position to be unique_id
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
        return $this->calendars[$this->lookup[$this->pos]];
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
        return isset($this->lookup[$this->pos]);
    }

    protected function fetchSettings() {
        if (isset($this->readonly['settings'])) {
            return;
        }

        $this->readonly['settings'] = new Settings($this->prepareCall(static::SETTINGS_URL)->request());
    }

    /**
     * @internal
     */
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
     * Create an HTTP request class
     * @param string The URL to request
     * @throws \RuntimeException If class $this->client does not implement \gClient\HTTP\ClientInterface
     * @throws \gClient\HTTP\Exception If the server returns a status code of 300 or greater
     * @throws \UnexpectedValueException If an invalid HTTP Method was set
     * @return \gClient\HTTP\ResponseInterface Instance of previously set requestor class
     */
    public function prepareCall($url) {
        return $this->connection->prepareCall($url)->addHeader(static::PROTOCOL_VERSION)->addHeader('Content-Type: ' . static::CONTENT_TYPE)->setParameter('alt', static::ALT);
    }

    public static function getClientLoginService() {
        return static::CLIENTLOGIN_SERVICE;
    }

    public static function getOAuthScope() {
        return static::OAUTH_SCOPE;
    }
}