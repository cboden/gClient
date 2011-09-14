<?php
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'SplClassLoader.php');

    $tests = new SplClassLoader('gClient', __DIR__);
    $tests->register();

    $app = new SplClassLoader('gClient', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib');
    $app->register();

// assertInternalType('string', 'foo bar')
//        $this->assertEquals('POST', $this->readAttribute($this->_client, 'method'));
