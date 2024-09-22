<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('MessageProvider\\', __DIR__ . '/src');
$loader->addPsr4('MessageProvider\\Tests\\', __DIR__ . '/tests');
