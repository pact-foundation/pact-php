<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('BinaryConsumer\\', __DIR__ . '/src');
$loader->addPsr4('BinaryConsumer\\Tests\\', __DIR__ . '/tests');
