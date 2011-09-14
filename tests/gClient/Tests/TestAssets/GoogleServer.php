<?php
namespace gClient\Tests\Calendar\TestAssets;
use gClient\Calendar;

class GoogleServer {
    protected $_url_lookup = Array();

    public function __construct() {
        $this->_url_lookup[Calendar\Service::ALL_LIST_URL]   = 'all_calendars';
        $this->_url_lookup[Calendar\Service::OWNER_LIST_URL] = 'own_calendars';
        $this->_url_lookup[Calendar\Service::SETTINGS_URL]   = 'settings';
    }

    public function __call($url, $params) {
        $method = $this->_url_lookup[$url];
        return call_user_func_array(Array($this, $method), $params);
    }

    protected function _settings() {
        
    }
}