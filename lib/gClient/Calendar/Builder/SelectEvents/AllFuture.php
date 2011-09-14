<?php
namespace gClient\Calendar\Builder\SelectEvents;
use gClient\Calendar\Builder\SelectEvents as myParent;

class AllFuture extends myParent {
    public function __construct() {
        $this->params['futureevents'] = 'true';
    }

    public function showDeleted() {
        $this->params['showdeleted'] = true;
        return $this;
    }
}