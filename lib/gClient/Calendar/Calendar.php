<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use DateTime;

/**
 * @note Thinking of saving a type of serialized entry of this
 *       use wakeup/sleep to avoid another connection
 *       This way Catalog can give data to Calendar w/o making another req
 */
class Calendar {
    protected $adapter;

    /**
     * @param string $url_src URL of the Google Calendar to work with
     * @param gClient\Auth\Adapter|NULL $adapter Authenticated account to use or null for an anonymouse (read-only) connection
     */
    public function __construct($url_src, Adapter $adapter = null) {
        if (is_null($adapter)) {
            $adapter = new Auth\Anonymous();
        }
        $this->adapter = $adapter;
    }

    /**
     * Fetch the scheduled events from this calendar between a specified dates
     * @param DateTime|NULL $from Start time or current time if NULL
     * @param DateTime|NULL $to End time or all future events if NULL
     * @returns gClient\Calendar\EventComposite ?
     */
    public function getEvents(DateTime $from = null, DateTime $to = null) {
    }
}