<?php
namespace gClient\Tests\Mock;
use gClient\Connection;

class Service implements \gClient\ServiceInterface {
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