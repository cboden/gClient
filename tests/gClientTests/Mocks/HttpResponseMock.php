<?php
namespace gClientTests\Mocks;
use gClient\HTTP\ClientResponse;

class HttpResponseMock implements ClientResponse {
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