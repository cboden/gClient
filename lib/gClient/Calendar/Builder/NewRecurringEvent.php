<?php
namespace gClient\Calendar\Builder;
use gClient\Calendar\Calendar;

class NewRecurringEvent extends NewEvent {
    public function __construct($title, \DatePeriod $period, Calendar $calendar = null) {
        $this->param['title'] = $title;

        // date period...

        $this->_calendar = $calendar;
    }
}