<?php
namespace gClient\Tests\Mocks;
use gClient\Connection;

class ServiceMock implements \gClient\ServiceInterface {
    public function __construct(Connection $connection) {
    }

    public function prepareCall($url) {
    }

    public static function getClientLoginService() {
        return 'SM';
    }

    public static function getOAuthScope() {
        return 'http://localhost/service/mock/scope';
    }
}