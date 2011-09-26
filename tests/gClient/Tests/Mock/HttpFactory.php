<?php
namespace gClient\Tests\Mock;
use gClient\HTTP\FactoryInterface;

class HttpFactory implements FactoryInterface {
    public function makeClient($url) {
        return new HttpClient($url, $this);
    }

    public function makeResponse($handle) {
        return new HttpResponse($handle);
    }
}