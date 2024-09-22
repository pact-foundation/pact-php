<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('XmlConsumer\\', __DIR__ . '/src');
$loader->addPsr4('XmlConsumer\\Tests\\', __DIR__ . '/tests');
