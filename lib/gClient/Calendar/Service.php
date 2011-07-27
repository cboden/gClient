<?php
namespace gClient\Calendar;
use gClient\Connection;
use gClient\HTTP;

/**
 * This is the main Calendar controlling class
 * It's class name is subject to change before 1.0 - I'm not stuck on it being an Iterator (::getCalendars() instead maybe)
 * 
 * @property Settings $settings A settings object of the Connection users' Google Calendar Settings
 * @link http://code.google.com/apis/calendar/data/2.0/developers_guide_protocol.html Calendar API Protocol documentation
 * @link http://code.google.com/apis/calendar/data/2.0/reference.html Calendar property reference
 */
class Service implements \gClient\ServiceInterface {
    const PROTOCOL_VERSION = 'GData-Version: 2';
    const CONTENT_TYPE     = 'application/json';
    const ALT              = 'jsonc';

    const ALL_LIST_URL   = '/calendar/feeds/default/allcalendars/full/';
    const OWNER_LIST_URL = '/calendar/feeds/default/owncalendars/full/';

    /**
     * Connection to use to make HTTP calls with
     * @var \gClient\Connection
     */
    protected $connection;

    protected $_collection;

    protected $_readonly = Array();

    /**
     * @param \gClient\Connection gData connection to make API calls through
     */
    public function __construct(Connection $connection) {
        $this->connection  = $connection;
        $this->_collection = new Collection();
    }

    /**
     * @return Collection
     */
    public function getCalendars() {
        $this->fetchCalendars();
        return $this->_collection;
    }

    public function __sleep() {
        return Array('connection');
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
        $this->fetchCalendars();

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
        return $this->_collection->insert(new Calendar($data['data'], $this));
    }

    /**
     * @param Calendar|string The Calendar instance, selfLink or unique_id of the calendar to delete
     * @return Service $this
     * @throws \gClient\HTTP\Exception
     */
    public function deleteCalendar($calendar) {
        $this->fetchCalendars();

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
        $this->_collection->remove($uid);

        return $this;
    }

    /**
     * Subscribe to a calendar
     * @param string ID of calendar to subscribe to
     * @return Calendar
     */
    public function subscribeToCalendar($id) {
        $this->fetchCalendars();

        $res = $this->prepareCall(static::ALL_LIST_URL)->method('POST')->setRawData(Array('data' => Array('id' => $id)))->request();

        $data = json_decode($res->getContent(), true);
// not sure if to return new calendar for $this
        return $this->_collection->insert(new Calendar($data['data'], $this));
    }

    /**
     * @param \gData\Calendar\Calendar|string The instance of Calendar to delete or the unique_id of the calendar
     * @throws \gClient\HTTP\Exception
     * @return Service $this
     */
    public function unsubscribeFromCalendar($calendar) {
        $this->fetchCalendars();

        $url = $calendar;
        if ($calendar instanceof Calendar) {
            $url = static::ALL_LIST_URL . $calendar->unique_id;
        }

        if (!(boolean)filter_var($url, FILTER_VALIDATE_URL)) {
            $url = static::ALL_LIST_URL . $url;
        }

        $this->prepareCall($url)->method('DELETE')->request();

        $uid = substr($url, strrpos($url, '/') + 1);
        $this->_collection->remove($uid);

        return $this;
    }

    /**
     * @internal
     */
    protected function fetchCalendars() {
        if ($this->_collection->count() > 0) {
            return;
        }

        $only_owner = false;

        $response = $this->prepareCall(((boolean)$only_owner ? static::OWNER_LIST_URL : static::ALL_LIST_URL))->method('GET')->request();
        $data = json_decode($response->getContent(), true);

        foreach ($data['data']['items'] as $i => $caldata) {
            $this->_collection->insert(new Calendar($caldata, $this));
        }
    }

    /**
     * @return bool
     * @internal
     */
    protected function fetchSettings() {
        if (isset($this->_readonly['settings'])) {
            return;
        }

        $this->_readonly['settings'] = new Settings($this);
    }

    /**
     * @internal
     */
    public function &__get($name) {
        if ($name == 'settings') {
            $this->fetchSettings();
        }

        if (!isset($this->_readonly[$name])) {
            $this->_readonly[$name] = '';
        }

        return $this->_readonly[$name];
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
        return 'cl';
    }

    public static function getOAuthScope() {
        return 'https://www.google.com/calendar/feeds/';
    }
}