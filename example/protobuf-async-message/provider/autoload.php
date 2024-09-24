<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('ProtobufAsyncMessageProvider\\Tests\\', __DIR__ . '/tests');
$loader->addPsr4('', __DIR__ . '/../library/src');
