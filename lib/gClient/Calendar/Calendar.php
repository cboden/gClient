<?php
namespace gClient\Calendar;
use gClient\Connection;
use gClient\Calendar\Service;
use DateTime;

/**
 * A Calendar class represents an individual calendar within Google, containing properties and events
 * @property string $unique_id The unique (Google wide) ID of the calendar
 * @property string $id The URL ID of the calendar
 * @property-write string $title Display name of the calendar
 * @property-write string $details
 * @property string $kind
 * @property string $etag
 * @property string $created
 * @property string $updated
 * @property string $eventFeedLink
 * @property string $accessControlListLink
 * @property string $selfLink
 * @property int $canEdit
 * @property Array $author
 * @property string $accessLevel none | read | freebusy | editor | owner | root
 * @property-write string $color
 * @property-write string $hidden
 * @property-write string $location
 * @property-write string $selected
 * @property-write string $timeZone
 * @property int $timesCleaned
 */
class Calendar extends \gClient\PropertyProxy {
    /**
     * Connection to use to make HTTP calls with
     * @var \gClient\Connection
     */
    protected $connection;

    /**
     * These are the valid colours (I'm Canadian) you can set a calendar to
     * @link http://code.google.com/apis/calendar/data/2.0/reference.html#gcal_reference
     */
    public static $valid_colors = Array(
          '#A32929', '#B1365F', '#7A367A', '#5229A3', '#29527A', '#2952A3', '#1B887A'
        , '#28754E', '#0D7813', '#528800', '#88880E', '#AB8B00', '#BE6D00', '#B1440E'
        , '#865A5A', '#705770', '#4E5D6C', '#5A6986', '#4A716C', '#6E6E41', '#8D6F47'
        , '#853104', '#691426', '#5C1158', '#23164E', '#182C57', '#060D5E', '#125A12'
        , '#2F6213', '#2F6309', '#5F6B02', '#8C500B', '#8C500B', '#754916', '#6B3304'
        , '#5B123B', '#42104A', '#113F47', '#333333', '#0F4B38', '#856508', '#711616'
    );

    /**
     * @param string URL of the Google Calendar to work with
     * @param gClient\Auth\Adapter|NULL Authenticated account to use or null for an anonymouse (read-only) connection
     * @todo Change $catalog_data to mixed - URL to fetch, Array if from Catalog
     */
    public function __construct(Array $properties, Connection $connection) {
        $properties['unique_id'] = substr($properties['id'], strrpos($properties['id'], '/') + 1);

        $this->connection = $connection;
        $this->setData($properties);
    }

    public function __sleep() {
        return Array('connection', 'readonly');
    }

    /**
     * Update one of the properties of this class
     * @param string Property name to udpate
     * @param string Value of property to update to
     * @throws \gClient\HTTP\Exception
     */
    public function update($property, $value) {
        $own_url = str_replace(Service::ALL_LIST_URL, Service::OWNER_LIST_URL, $this->selfLink);
        $res = $this->prepareCall($own_url)->method('PUT')->setRawData(Array('data' => Array($property => $value)))->request();
        $this->info[$property] = $value;
    }

    /**
     * Fetch the scheduled events from this calendar between a specified dates
     * @param EventSelector|NULL Set the parameters of which events to fetch
     * @throws /gClient\HTTP\Exception
     * @return \SplFixedArray of Event
     */
    public function getEvents(EventSelector $query = null) {
        if ($query === null) {
            $query = new EventSelector\AllFuture();
        }

        $res = $this->prepareCall($this->eventFeedLink)->method('GET')->setParameters($query->params)->request();
        $data = json_decode($res->getContent(), true);

        if (!isset($data['data']['items'])) {
            return new \SplFixedArray(0);
        }

        $events = new \SplFixedArray(count($data['data']['items']));
        foreach ($data['data']['items'] as $i => $edata) {
            $events[$i] = new Event($edata, $this->connection);
        }

        return $events;
    }

    /**
     * @param Event|string Either a Event with all the set parameters or a string to parse as an event ex. "Squash with Chris tomorrow at noon"
     * @return Event
     */
    public function createEvent($event) {
    }

    /**
     * @deprecated possibly
     */
    public function createAllDayEvent(DateTime $date) {
    }

    /**
     * @param Event|string Event instance or ID of the event to delete from this calendar
     * @throws \gClient\HTTP\Exception
     */
    public function deleteEvent($event) {
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
        return $this->connection->prepareCall($url)->addHeader(Service::PROTOCOL_VERSION)->addHeader('Content-Type: ' . Service::CONTENT_TYPE)->setParameter('alt', Service::ALT);
    }
}