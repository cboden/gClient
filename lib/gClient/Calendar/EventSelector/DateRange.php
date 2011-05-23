<?php
namespace gClient\Calendar\EventSelector;
use gClient\Calendar\EventSelector as myParent;

class DateRange extends myParent {
    public function __construct(\DateTime $from, \DateTime $to) {
        $this->setRange($from, $to);
    }

    public function setRange(\DateTime $from , \DateTime $to) {
        $this->params['start-min'] = $from->format(\DateTime::RFC3339);
        $this->params['start-max'] = $to->format(\DateTime::RFC3339);

        return $this;
    }

    public function showDeleted() {
        $this->params['showdeleted'] = true;
        return $this;
    }
}