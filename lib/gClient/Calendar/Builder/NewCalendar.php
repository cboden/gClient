<?php
namespace gClient\Calendar\Builder;
use gClient\Calendar\Service;

class NewCalendar {
    public $params = Array();

    /**
     * @type gClient\Calendar\Service
     */
    protected $_service;

    /**
     * @param string
     */
    public function __construct($title, Service $service = null) {
        $this->params['title'] = (string)$title;
        $this->_service = $service;
    }

    /**
     * @return SplFixedArray
     */
    public static function getRequiredFields() {
        $req    = new \SplFixedArray(1);
        $req[0] = 'title';

        return $req;
    }

    /**
     * @return SplFixedArray
     */
    public static function getOptionalFields() {
        $opt    = new \SplFixedArray(5);

        $opt[0] = 'details';
        $opt[1] = 'timezone';
        $opt[2] = 'hidden';
        $opt[3] = 'color';
        $opt[4] = 'location';

        return $opt;
    }

    /**
     * @param string
     * @return NewCalendar
     */
    public function setDetails($details) {
        $this->params['details'] = (string)$details;

        return $this;
    }

    /**
     * @param DateTimeZone
     * @return NewCalendar
     */
    public function setTimeZone(\DateTimeZone $timezone) {
        $this->params['timezone'] = $timezone->getName();

        return $this;
    }

    /**
     * @param boolean
     * @return NewCalendar
     */
    public function setHidden($hidden) {
        $this->params['hidden'] = (boolean)$hidden;

        return $this;
    }

    /**
     * @param string
     * @return NewCalendar
     */
    public function setColor($colour) {
        // validate...I need to have some kind of common interface/datatype/class for Calendars and Builders

        $this->params['color'] = (string)$colour;

        return $this;
    }

    /**
     * @param string
     * @return NewCalendar
     * @alias setColor
     */
    public function setColour($colour) {
        return $this->setColor($colour);
    }

    /**
     * @param string
     * @return NewCalendar
     */
    public function setLocation($location) {
        $this->params['location'] = (string)$location;

        return $this;
    }

    /**
     * @return gClient\Calendar\Calendar
     */
    public function flush() {
        if (!($this->_service instanceof Service)) {
            throw new \RuntimeException('There is no account attached to this builder');
        }

        return $this->_service->createCalendar($this);
    }
}