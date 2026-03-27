<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('ServerSentEventsConsumer\\', __DIR__ . '/src');
$loader->addPsr4('ServerSentEventsConsumer\\Tests\\', __DIR__ . '/tests');
