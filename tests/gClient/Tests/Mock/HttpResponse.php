<?php
namespace gClient\Tests\Mock;
use gClient\HTTP\ClientResponse;

class HttpResponse implements ClientResponse {
    public $_status_code;
    public $_header;
    public $_content;

    public function __construct($response) {
    }

    public function getStatusCode() {
    }

    public function getHeaderItem($key) {
    }

    public function getHeader() {
    }

    public function getContent() {
    }
}