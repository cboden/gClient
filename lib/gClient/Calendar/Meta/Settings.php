<?php
namespace gClient\Calendar\Meta;
use gClient\Calendar;
use gClient\Calendar\Service;
use gClient\Calendar\Calendar as Cal;

/**
 * Settings are the changable attributes associated with a calendar
 * @property-write string $title Display name of the calendar
 * @property-write string $details
 * @property-write string $color
 * @property-write string $hidden
 * @property-write string $location
 * @property-write string $selected
 * @property-write string $timeZone
 * @todo consider creating __set() aliasing update() 
 */
class Settings implements \IteratorAggregate {
    /**
     * @internal
     */
    protected $_calendar;

    /**
     * @internal
     */
    protected $_magic = Array();

    /**
     * These are the valid colours (I'm Canadian) you can set a calendar to
     * In the event Google allows more colours without this being updated, it's a public property, able to be updated
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
     * @internal
     */
    public function __construct(Cal $calendar) {
        $this->_calendar = $calendar;
    }

    /**
     * @internal
     */
    public function __get($name) {
        if (!isset($this->_magic[$name])) {
            return '';
        }

        return $this->_magic[$name];
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->_magic);
    }

    /**
     * Update one of the properties of this class
     * @param string Property name to udpate
     * @param string Value of property to update to
     * @throws \gClient\HTTP\Exception
     * @return null
     */
    public function update($setting, $value) {
        $own_url = str_replace(Service::ALL_LIST_URL, Service::OWNER_LIST_URL, $this->_calendar->properties->selfLink);
        $res = $this->_calendar->prepareCall($own_url)->setMethod('PUT')->setRawData(Array('data' => Array($setting => $value)))->request();
        $this->_magic[$setting] = $value;
    }

    public function getNames() {
        return Array('title', 'details', 'color', 'hidden', 'location', 'selected', 'timeZone');
    }

    /**
     * @param Array key/value paired array to set Calendar properties to
     * @internal
     */
    public function softSetValues(Array $attributes) {
        $this->_magic = $attributes;
    }
}