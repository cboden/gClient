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