<?php
namespace gClient\Tests\Mock;
use gClient\HTTP\ClientInterface;

class HttpClient implements ClientInterface {
    public $_url;
    public $_method;
    public $_params  = Array();
    public $_headers = Array();

    public function __construct($url) {
        $this->_url = $url;
    }

    public function setMethod($method) {
        $this->_method = $method;
    }

    public function setRawData($data) {
        $this->_params = $data;
    }

    public function setParameter($key, $val) {
        $this->_params[$key] = $val;
    }

    public function addHeader($header) {
        $this->_headers[] = $header;
    }

    public function setParameters(Array $parameters = Array()) {
        $this->_params = $parameters + $this->_params;
    }

    public function addHeaders(Array $headers = Array()) {
        $this->_headers = $headers + $this->_headers;
    }

    public function request() {
        return new HttpResponseMock($this);
    }
}