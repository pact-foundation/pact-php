<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('GraphqlConsumer\\', __DIR__ . '/src');
$loader->addPsr4('GraphqlConsumer\\Tests\\', __DIR__ . '/tests');
