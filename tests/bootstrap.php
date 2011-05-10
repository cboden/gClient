<?php
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'SplClassLoader.php');

    $loader = new SplClassLoader('gClient', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib');
    $loader->register();
