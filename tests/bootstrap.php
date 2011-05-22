<?php
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'SplClassLoader.php');

    $app = new SplClassLoader('gClient', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib');
    $app->register();

    $tests = new SplClassLoader('gClientTests', __DIR__);
    $tests->register();

// assertInternalType('string', 'foo bar')
//        $this->assertEquals('POST', $this->readAttribute($this->_client, 'method'));