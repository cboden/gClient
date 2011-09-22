<?php
namespace gClient\Calendar\Builder;
use gClient\Calendar\Calendar;

class NewEvent {
    public $params = Array(
        'when' => Array()
    );

    /**
     * @type gClient\Calendar\Calendar
     * @internal
     */
    protected $_calendar;

    /**
     * @param string
     * @param DateTime
     * @param DateTime
     * @param gClient\Calendar\Calendar
     */
    public function __construct($title, \DateTime $start, \DateTime $end, Calendar $service = null) {
        $this->params['title']         = (string)$title;
        $this->params['when'][] = Array(
            'start' => $start->format(\DateTime::RFC3339)
          , 'end'   => $end->format(\DateTime::RFC3339)
        );

        $this->_calendar = $service;
    }

    /**
     * @return SplFixedArray
     */
    public static function getRequiredFields() {
        $req    = new \SplFixedArray(3);
        $req[0] = 'title';
        $req[1] = 'start';
        $req[2] = 'end';

        return $req;
    }

    /**
     * @return SplFixedArray
     */
    public static function getOptionalFields() {
        $opt    = new \SplFixedArray(4);

        $opt[0] = 'details';
        $opt[2] = 'transparency';
        $opt[3] = 'status';
        $opt[4] = 'location';

        return $opt;
    }

    /**
     * @param string
     * @return NewEvent
     */
    public function setDetails($details) {
        $this->params['details'] = (string)$details;
        return $this;
    }

    /**
     * @param string
     * @return NewEvent
     */
    public function setTransparency($trans) {
        $this->params['transparency'] = (string)$trans;
        return $this;
    }

    /**
     * @param string
     * @return NewEvent
     */
    public function setStatus($status) {
        $this->params['status'] = (string)$status;
        return $this;
    }

    /**
     * @param string
     * @return NewEvent
     */
    public function setLocation($location) {
        $this->params['location'] = (string)$location;
        return $this;
    }

    /**
     * @return gClient\Calendar\Event
     */
    public function flush() {
        return $this->_calendar->createEvent($this);
    }
}