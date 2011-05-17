<?php
namespace gClientTests\Calendar\TestAssets;
use gclient\Auth\Adapter as A;
use gClient\Calendar;

class GoogleServer {
    protected $_url_lookup = Array();

    public function __construct() {
        $this->_url_lookup[Calendar\Catalog::SETTINGS_URL] = 'settings';
    }

    public function __call($url, $params) {
        $method = $this->_url_lookup[$url];
        return call_user_func_array(Array($this, $method), $params);
    }

    protected function _settings() {
        
    }
}