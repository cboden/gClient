<?php
namespace gClient\Tests\Mock;
use gClient\HTTP\ClientInterface;
use gClient\HTTP\FactoryInterface;

class HttpClient implements ClientInterface {
    public $_factory;
    public $_url;
    public $_method;
    public $_params  = Array();
    public $_headers = Array();

    public function __construct($url, FactoryInterface $factory) {
        $this->_url     = $url;
        $this->_factory = $factory;
    }

    /**
     * This part of the Mock object allows the test suite
     * to pre-determine what response will be given
     * despite data collected
     */
    // public function preSetResponse()

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
        return new HttpResponse($this);
    }
}