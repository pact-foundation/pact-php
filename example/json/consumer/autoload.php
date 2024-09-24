<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('JsonConsumer\\', __DIR__ . '/src');
$loader->addPsr4('JsonConsumer\\Tests\\', __DIR__ . '/tests');
