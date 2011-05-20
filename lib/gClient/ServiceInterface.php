<?php
namespace gClient;

interface ServiceInterface {
    public function __construct(Connection $connection);
}