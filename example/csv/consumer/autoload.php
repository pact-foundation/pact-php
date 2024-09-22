<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('CsvConsumer\\', __DIR__ . '/src');
$loader->addPsr4('CsvConsumer\\Tests\\', __DIR__ . '/tests');
