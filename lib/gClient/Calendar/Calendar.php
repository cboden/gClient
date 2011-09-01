<?php
namespace gClient\Calendar;
use gClient\Connection;
use gClient\Calendar\Service;
use gClient\Calendar\Meta;
use DateTime;

/**
 * A Calendar class represents an individual calendar within Google, containing properties and events
 * @property string $unique_id The unique (Google wide) ID of the calendar
 * @property Meta\Settings $settings Changeable options pertaining to the calendar
 * @property Meta\Properties $properties Read-only attributes associated with the calendar
 * @property Meta\Sharing $sharing Manage who has what access to the calendar
 * @property Meta\Extensions $extensions User defined properties
 */
class Calendar {
    /**
     * Service to use to make HTTP calls with
     * @var Service
     */
    protected $service;

    protected $_magic = Array(
        'settings'   => null
      , 'properties' => null
      , 'sharing'    => null
      , 'extensions' => null

      , 'unique_id'  => null
    );

    /**
     * @internal
     * @param Array Data passed by Service
     * @param \gClient\ServiceInterface Authenticated account to use or null for an anonymouse (read-only) connection
     * @todo Change $catalog_data to mixed - URL to fetch, Array if from Catalog
     */
    public function __construct(Array $properties, \gClient\ServiceInterface $service) {
        $this->_magic['unique_id'] = substr($properties['id'], strrpos($properties['id'], '/') + 1);

        foreach (Array('settings', 'properties') as $attribute) {
            $ns = __NAMESPACE__ . '\\Meta\\' . $attribute;
            $this->_magic[$attribute] = new $ns($this);

            $values   = Array();
            $required = $this->_magic[$attribute]->getNames();
            foreach ($required as $key) {
                if (isset($properties[$key])) {
                    $values[$key] = $properties[$key];
                }
            }

            $this->_magic[$attribute]->softSetValues($values);
        }

        $this->service = $service;
    }

    public function __sleep() {
        return Array('service', '_magic');
    }

    /**
     * Update one of the properties of this class
     * @param string Property name to udpate
     * @param string Value of property to update to
     * @throws \gClient\HTTP\Exception
     * @return void
     */
    public function update($property, $value) {
        $own_url = str_replace(Service::ALL_LIST_URL, Service::OWNER_LIST_URL, $this->selfLink);
        $res = $this->service->prepareCall($own_url)->setMethod('PUT')->setRawData(Array('data' => Array($property => $value)))->request();
// need to udpate settings/property
//        $this->info[$property] = $value;
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

        $res = $this->service->prepareCall($this->eventFeedLink)->setMethod('GET')->setParameters($query->params)->request();
        $data = json_decode($res->getContent(), true);

        if (!isset($data['data']['items'])) {
            return new \SplFixedArray(0);
        }

        $events = new \SplFixedArray(count($data['data']['items']));
        foreach ($data['data']['items'] as $i => $edata) {
            $events[$i] = new Event($edata, $this);
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
     * @internal
     */
    public function &__get($name) {
        if (!isset($this->_magic[$name])) {
            $this->_magic[$name] = '';
        }

        return $this->_magic[$name];
    }
}