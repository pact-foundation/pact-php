<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('JsonProvider\\', __DIR__ . '/src');
$loader->addPsr4('JsonProvider\\Tests\\', __DIR__ . '/tests');
