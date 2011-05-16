<?php
namespace gClient\Calendar;

/**
 * Read-only Google Calendar settings
 * @property string $alternateCalendar
 * @property string $country ISO 3166-1-alpha-2 (2 letter country code)
 * @property string $customCalMode 
 * @property string $dateFieldOrder
 * @property string $defaultCalMode Preferred calendar view (day|week|month|custom|agenda)
 * @property string $displayAllTimezones
 * @property boolean $format24HourTime AM/PM or 24 military hour display
 * @property boolean $hideInvitations
 * @property boolean $hideWeekends
 * @property string $locale 2 letter language code
 * @property boolean $showDeclinedEvents
 * @property string $timezone Default timezone to create events in
 * @property string $timezoneLabel User defined label for default timezone
 * @property string $userLocation User entered value used by the $weather setting
 * @property string $weather (C|F) if the user selected a local weather calendar
 * @property int $weekStart (0|1|6) translates to (Sunday|Monday|Saturday)
 */
class Settings {
    /**
     * Raw data sent back from Google
     */
    protected $data;

    /**
     * Individual parsed settings given through __get
     * @var Array
     */
    protected $settings = Array();

    public function __construct(\gClient\HTTP\ResponseInterface $response) {
        $data = json_decode($response->getContent(), true);
        $this->data = $data['data'];

        foreach ($data['data']['items'] as $i => $contents) {
            $this->settings[$contents['id']] = $contents['value'];
        }
    }

    public function &__get($name) {
        if (!isset($this->settings[$name])) {
            $this->settings[$name] = '';
        }

        return $this->settings[$name];
    }
}