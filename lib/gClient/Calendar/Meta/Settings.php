<?php
namespace gClient\Calendar\Meta;
use gClient\Calendar;
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
 */
class Settings {
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

    public function &__get($name) {
        if (!isset($this->_magic[$name])) {
            $this->_magic[$name] = '';
        }

        return $this->_magic[$name];
    }

    public function set($key, $val) {
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