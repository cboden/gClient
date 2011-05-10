<?php
namespace gClientTests\Auth\TestAssets;
use gClient\Auth\Adapter;

class MockAdapter extends Adapter {
    public function getHeaderString() {
        return 'Hello World!';
    }
}