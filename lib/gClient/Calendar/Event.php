<?php
namespace gClient\Calendar;

/**
 * Representation of a single event in Google Calendars
 * @property string $id
 * @property string $title
 * @property string $kind
 * @property string $etag
 * @property string $selfLink
 * @property string $alternateLink
 * @property int $canEdit
 * @property string $created
 * @property string $updated
 * @property string $details
 * @property string $status
 * @property Array $creator
 * @property int $anyoneCanAddSelf
 * @property int $guestsCanInviteOthers
 * @property int $guestsCanModify
 * @property int $guestsCanSeeGuests
 * @property int $sequence
 * @property string $transparency
 * @property string $visibility
 * @property string $location
 * @property array $attendees
 * @property array $when
 */
class Event {
    protected $_calendar;
    protected $_properties;

    public function __construct(Array $data, Calendar $calendar) {
        $this->_calendar   = $calendar;
        $this->_properties = $data;
    }

    public function __get($name) {
        if (!isset($this->properties[$name])) {
            return '';
        }

        return $this->properties[$name];
    }
}