<?php
namespace gClientTests\Auth\TestAssets;
use gClient\Auth\Adapter;

class MockAdapter extends Adapter {
    public function __construct($connect_successfully = true) {
        $this->token = (int)$connect_successfully;
    }

    public function getHeaderString() {
        return 'Auth: MockLogin %s';
    }
}