<?php
namespace gClient\Calendar;

abstract class EventSelector {
    public $params = Array(
        'orderby' => 'starttime'
    );

    public function setTimeZone(\DateTimeZone $ctz) {
        $this->params['ctz'] = '';
        return $this;
    }

    public function sortAscending() {
        $this->params['sortorder'] = 'ascending';
        return $this;
    }

    public function sortDecending() {
        $this->params['sortorder'] = 'descending';
        return $this;
    }

    public function showHidden() {
        $this->params['showhidden'] = true;
        return $this;
    }
}