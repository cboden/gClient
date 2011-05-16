<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use DateTime;

/**
 * @property string $id The unique ID of the calendar
 * @property string $title Display name of the calendar
 * @property string $details
 * @property string $kind
 * @property string $etag
 * @property string $created
 * @property string $updated
 * @property string $eventFeedLink
 * @property string $accessControlListLink
 * @property string $selfLink
 * @property int $canEdit
 * @property Array $author
 * @property string $accessLevel
 * @property string $color
 * @property string $hidden
 * @property string $location
 * @property string $selected
 * @property string $timeZone
 * @property int $timesCleaned
 */
class Calendar {
    /**
     * Adapter to use to make HTTP calls with
     * @var \gClient\Auth\Adapter
     */
    protected $adapter;

    /**
     * Data from Google associated with it
     * @var Array
     */
    protected $info;

    /**
     * @param string URL of the Google Calendar to work with
     * @param gClient\Auth\Adapter|NULL Authenticated account to use or null for an anonymouse (read-only) connection
     * @todo Change $catalog_data to mixed - URL to fetch, Array if from Catalog
     */
    public function __construct(Array $catalog_data, Adapter $adapter = null) {
        if (is_null($adapter)) {
            $adapter = new Auth\Anonymous();
        }
        $this->adapter = $adapter;

        $this->info = $catalog_data;
    }

    public function __get($var) {
        return isset($this->info[$var]) ? $this->info[$var] : '';
    }

    public function __sleep() {
        return Array('adapter', 'info');
    }

    /**
     * Fetch the scheduled events from this calendar between a specified dates
     * @param DateTime|NULL Start time or current time if NULL
     * @param DateTime|NULL End time or all future events if NULL
     * @return gClient\Calendar\EventComposite ?
     */
    public function getEvents(DateTime $from = null, DateTime $to = null) {
    }
}