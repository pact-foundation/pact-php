<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('FormUrlEncodedConsumer\\', __DIR__ . '/src');
$loader->addPsr4('FormUrlEncodedConsumer\\Tests\\', __DIR__ . '/tests');
