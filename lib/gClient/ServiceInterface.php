<?php
namespace gClient;

interface ServiceInterface {
    public function __construct(Connection $connection);

    public static function getClientLoginService();
    public static function getOAuthScope();
}