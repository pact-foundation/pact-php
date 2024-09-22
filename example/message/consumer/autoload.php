<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('MessageConsumer\\', __DIR__ . '/src');
$loader->addPsr4('MessageConsumer\\Tests\\', __DIR__ . '/tests');
