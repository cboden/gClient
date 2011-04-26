<?php
namespace cB\gData\Calendar;
use cB\gData\Auth\Adapter;

use DateTime;

/**
 * Thinking of saving a type of serialized entry of this
 * use wakeup/sleep to avoid another connection
 * This way Catalog can give data to Calendar w/o making another req
 * 
 */

class Calendar {
    protected $adapter;

    public function __construct($url_src, Adapter $adapter = null) {
        if (is_null($adapter)) {
            $adapter = new Auth\Anonymous();
        }
        $this->adapter = $adapter;
    }

    // From: Now
    // To: End of Time!
    public function getEvents(DateTime $from = null, DateTime $to = null) {
    }
}