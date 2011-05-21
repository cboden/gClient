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
        $this->data = $data['data'];
    }

    /**
     * @param string $name Name of calendar to create
     * @param Array Additional creational params - associative keys: (details, timeZone, hidden, color, location)
     * @return Calendar
     * @throws \UnexpectedValueException On an empty name/title or invalid color
     * @throws \gClient\HTTP\Exception On a bad return from Google
     * @todo This appends to end of all calendars - should append to end of owner calendars
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
     * @param Calendar|string ID of the calendar to delete
     * @return Service $this
     * @throws \gClient\HTTP\Exception
     */
    public function deleteCalendar($calendar) {
        $url = $calendar;
        if ($calendar instanceof Calendar) {
            $url = $calendar->selfLink;
        }

        $own_url = str_replace(static::ALL_LIST_URL, static::OWNER_LIST_URL, $url);

        $res = $this->prepareCall($own_url)->method('DELETE')->request();
        if ($res->getStatusCode() != 200) {
            throw new HTTP\Exception($res);
        }

        $pos = 0;
        foreach ($this->data['items'] as $i => $info) {
            if ($info['selfLink'] == $url) {
                $pos = $i;
                break;
            }
        }

        array_splice($this->data['items'], $pos, 1);
        if (isset($this->calendars[$pos])) {
            array_splice($this->calendars, $pos, 1);
        }

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

    public function unsubscribeFromCalendar($id) {
    }

    /**
     * @param Array
     * @return Calendar
     */
    protected function appendCalendar($data) {
        array_push($this->data['items'], $data);

        end($this->data['items']);
        $key = key($this->data['items']);

        $this->calendars[$key] = new Calendar($data, $this->connection);

        return $this->calendars[$key];
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
            $this->calendars[$this->pos] = new Calendar($this->data['items'][$this->pos], $this->connection);
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