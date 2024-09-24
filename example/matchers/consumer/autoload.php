<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('MatchersConsumer\\', __DIR__ . '/src');
$loader->addPsr4('MatchersConsumer\\Tests\\', __DIR__ . '/tests');
