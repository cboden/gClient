<?php
namespace gClient\Calendar\Meta;
use gClient\Calendar;
use gClient\Calendar\Calendar as Cal;

/**
 * Properties are read-only attributes of a calendar
 * @property string $id The URL ID of the calendar
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
 * @property int $timesCleaned
 */
class Properties {
    /**
     * @internal
     */
    protected $_calendar;

    /**
     * @internal
     */
    protected $_magic = Array();

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

    public function getNames() {
        return Array('id', 'kind', 'etag', 'created', 'updated', 'eventFeedLink', 'accessControlListLink', 'selfLink', 'canEdit', 'author', 'accessLevel', 'timesCleaned');
    }

    /**
     * @param Array key/value paired array to set Calendar properties to
     * @internal
     */
    public function softSetValues(Array $attributes) {
        $this->_magic = $attributes;
    }
}