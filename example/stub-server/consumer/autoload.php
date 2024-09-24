<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('StubServerConsumer\\', __DIR__ . '/src');
$loader->addPsr4('StubServerConsumer\\Tests\\', __DIR__ . '/tests');
