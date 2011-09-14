<?php
namespace gClient\Calendar;
use gClient\Connection;
use gClient\Calendar\Service\Settings;
use gClient\Calendar\Service\Collection;
use gClient\Calendar\Builder\NewCalendar;
use gClient\Calendar\Meta\Settings as CalSettings;
use gClient\HTTP;

/**
 * This is the main Calendar controlling class
 * @property \gClient\Calendar\Service\Settings $settings A settings object of the Connection users' Google Calendar Settings
 * @property \gClient\Calendar\Service\Collection $calendars An iterator of the calendars in this service
 * @link http://code.google.com/apis/calendar/data/2.0/developers_guide_protocol.html Calendar API Protocol documentation
 * @link http://code.google.com/apis/calendar/data/2.0/reference.html Calendar property reference
 */
class Service implements \gClient\ServiceInterface, \IteratorAggregate {
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

    /**
     * Container for various read only properties proxied through __get
     * @internal
     * @var Array
     */
    protected $_magic = Array(
        'calendars' => null
      , 'settings'  => null
    );

    /**
     * @param \gClient\Connection gData connection to make API calls through
     */
    public function __construct(Connection $connection) {
        $this->connection          = $connection;
        $this->_magic['calendars'] = new Collection();
    }

    public function __sleep() {
        return Array('connection');
    }

    /**
     * @return Iterator
     */
    public function getIterator() {
        return $this->calendars;
    }

    /**
     * @param string Builder\NewCalendar A builder object containing the parameters for creating a new calendar
     * @return Calendar
     * @throws \UnexpectedValueException On an empty name/title or invalid color
     * @throws \gClient\HTTP\Exception On a bad return from Google
     * @todo This appends to end of all calendars - should append to end of owner calendars - including alphabetical order
     * @todo Consider setting timeZone from Settings if not set in $attributes
     */
    public function createCalendar(NewCalendar $new) {
        $this->fetchCalendars();

        $content = $new->params;

        if (is_null($content['title']) || empty($content['title'])) {
            throw new \UnexpectedValueException('Calendar name/title should be set');
        }

        if (isset($content['color']) && !in_array($content['color'], CalSettings::$valid_colors)) {
            throw new \UnexpectedValueException("{$content['color']} is not a valid calendar color");
        }

        $res = $this->prepareCall(static::OWNER_LIST_URL)->setMethod('POST')->setRawData(Array('data' => $content))->request();
        if (201 != ($http_code = $res->getStatusCode())) {
            throw new HTTP\Exception($res);
        }

        $data = json_decode($res->getContent(), true);
        return $this->_magic['calendars']->insert(new Calendar($data['data'], $this));
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

        $res = $this->prepareCall($own_url)->setMethod('DELETE')->request();
        if ($res->getStatusCode() != 200) {
            throw new HTTP\Exception($res);
        }

        $uid = substr($own_url, strrpos($own_url, '/') + 1);
        $this->_magic['calendars']->remove($uid);

        return $this;
    }

    /**
     * Subscribe to a calendar
     * @param string ID of calendar to subscribe to
     * @return Calendar
     */
    public function subscribeToCalendar($id) {
        $this->fetchCalendars();

        $res = $this->prepareCall(static::ALL_LIST_URL)->setMethod('POST')->setRawData(Array('data' => Array('id' => $id)))->request();

        $data = json_decode($res->getContent(), true);
// not sure if to return new calendar for $this
        return $this->_magic['calendars']->insert(new Calendar($data['data'], $this));
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

        $this->prepareCall($url)->setMethod('DELETE')->request();

        $uid = substr($url, strrpos($url, '/') + 1);
        $this->_magic['calendars']->remove($uid);

        return $this;
    }

    public function buildCalendar($title) {
        $class   = __NAMESPACE__ . '\\Builder\\NewCalendar';
        $builder = new $class($title, $this);

        return $builder;
    }

    /**
     * @internal
     */
    protected function fetchCalendars() {
        if ($this->_magic['calendars']->count() > 0) {
            return;
        }

        $only_owner = false;

        $response = $this->prepareCall(((boolean)$only_owner ? static::OWNER_LIST_URL : static::ALL_LIST_URL))->setMethod('GET')->request();
        $data = json_decode($response->getContent(), true);

        foreach ($data['data']['items'] as $i => $caldata) {
            $this->_magic['calendars']->insert(new Calendar($caldata, $this));
        }
    }

    /**
     * @return bool
     * @internal
     */
    protected function fetchSettings() {
        if (null !== $this->_magic['settings']) {
            return;
        }

        $this->_magic['settings'] = new Settings($this);
    }

    /**
     * @internal
     */
    public function &__get($name) {
        // need a more elegant way to do this
        if ($name == 'settings') {
            $this->fetchSettings();
        } elseif ($name == 'calendars') {
            $this->fetchCalendars();
        }

        if (!isset($this->_magic[$name])) {
            $this->_magic[$name] = '';
        }

        return $this->_magic[$name];
    }

    /**
     * Create an HTTP request class to manually make a request to Google's API
     * @param string The URL to request
     * @throws \RuntimeException If class $this->client does not implement \gClient\HTTP\ClientInterface
     * @throws \gClient\HTTP\Exception If the server returns a status code of 300 or greater
     * @throws \UnexpectedValueException If an invalid HTTP Method was set
     * @return \gClient\HTTP\ResponseInterface Instance of previously set requestor class
     */
    public function prepareCall($url) {
        return $this->connection->prepareCall($url)->addHeader(static::PROTOCOL_VERSION)->addHeader('Content-Type: ' . static::CONTENT_TYPE)->setParameter('alt', static::ALT);
    }

    /**
     * @return string
     */
    public static function getClientLoginService() {
        return 'cl';
    }

    /**
     * @return string
     */
    public static function getOAuthScope() {
        return 'https://www.google.com/calendar/feeds/';
    }
}