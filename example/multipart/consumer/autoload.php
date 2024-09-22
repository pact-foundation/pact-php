<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('MultipartConsumer\\', __DIR__ . '/src');
$loader->addPsr4('MultipartConsumer\\Tests\\', __DIR__ . '/tests');
