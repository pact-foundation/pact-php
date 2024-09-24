<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('ProtobufSyncMessageConsumer\\', __DIR__ . '/src');
$loader->addPsr4('ProtobufSyncMessageConsumer\\Tests\\', __DIR__ . '/tests');
$loader->addPsr4('', __DIR__ . '/../library/src');
