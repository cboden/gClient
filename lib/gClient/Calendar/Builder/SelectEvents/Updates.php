<?php
namespace gClient\Calendar\Builder\SelectEvents;
use gClient\Calendar\Builder\SelectEvents as myParent;

class Updates extends myParent {
    public function __construct(\DateTime $since) {
        $this->params['orderby'] = 'lastmodified';
        $this->setSince($since);
    }

    public function setSince(\DateTime $since) {
        $this->params['updated-min'] = $since->format(\DateTime::RFC3339);
        return $this;
    }
}