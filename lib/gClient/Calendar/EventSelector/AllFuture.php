<?php
namespace gClient\Calendar\EventSelector;
use gClient\Calendar\EventSelector as myParent;

class AllFuture extends myParent {
    public function __construct() {
        $this->params['futureevents'] = 'true';
    }

    public function showDeleted() {
        $this->params['showdeleted'] = true;
        return $this;
    }
}