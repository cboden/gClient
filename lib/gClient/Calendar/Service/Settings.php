<?php
namespace gClient\Calendar\Service;
use gClient\Calendar\Service;

/**
 * Read-only Google Calendar settings
 * @property int $alternateCalendar (0|1|2|3)
 * @property string $country ISO 3166-1-alpha-2 (2 letter country code)
 * @property string $customCalMode 
 * @property string $dateFieldOrder (DMY|MDY)
 * @property string $defaultCalMode Preferred calendar view (day|week|month|custom|agenda)
 * @property boolean $displayAllTimezones
 * @property boolean $format24HourTime AM/PM or 24 military hour display
 * @property boolean $hideInvitations
 * @property boolean $hideWeekends
 * @property string $locale ISO 639-1 2 letter language code
 * @property boolean $showDeclinedEvents Whether to display events you have declined to attend in your calendars
 * @property string $timezone Default timezone to create events in
 * @property string $timezoneLabel User defined label for default timezone
 * @property string $userLocation User entered value used by the $weather setting
 * @property string $weather (C|F) if the user selected a local weather calendar
 * @property int $weekStart (0|1|6) translates to (Sunday|Monday|Saturday)
 * @todo Implement iterator
 */
class Settings implements \IteratorAggregate {
    const URL = '/calendar/feeds/default/settings/';

    protected $_readonly = Array();

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->_readonly);
    }

    /**
     * @internal
     */
    public function __construct(Service $service) {
        $response = $service->prepareCall(static::URL)->request();
        $data     = json_decode($response->getContent(), true);

        // might need to save the URLs
        foreach ($data['data']['items'] as $i => &$sdata) {
            $this->_readonly[$sdata['id']] = $sdata['value'];
        }
    }

    /**
     * @internal
     */
    public function __get($name) {
        if (!isset($this->_readonly[$name])) {
            return '';
        }

        return $this->_readonly[$name];
    }
}