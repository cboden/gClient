<?php
namespace gClient\Calendar\Builder;

class NewCalendar {
    public $params = Array();

    /**
     * @param string
     */
    public function __construct($name) {
        $this->params['title'] = (string)$name;
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
}