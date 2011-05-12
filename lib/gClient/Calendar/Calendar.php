<?php
namespace gClient\Calendar;
use gClient\Auth\Adapter;
use DateTime;

class Calendar {
    protected $adapter;
    protected $info;

    /**
     * @param string $url_src URL of the Google Calendar to work with
     * @param gClient\Auth\Adapter|NULL $adapter Authenticated account to use or null for an anonymouse (read-only) connection
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
     * @param DateTime|NULL $from Start time or current time if NULL
     * @param DateTime|NULL $to End time or all future events if NULL
     * @returns gClient\Calendar\EventComposite ?
     */
    public function getEvents(DateTime $from = null, DateTime $to = null) {
    }
}