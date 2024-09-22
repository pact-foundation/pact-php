<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('GeneratorsConsumer\\', __DIR__ . '/src');
$loader->addPsr4('GeneratorsConsumer\\Tests\\', __DIR__ . '/tests');
