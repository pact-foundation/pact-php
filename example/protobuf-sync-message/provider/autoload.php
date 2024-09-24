<?php

$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('ProtobufSyncMessageProvider\\', __DIR__ . '/src');
$loader->addPsr4('ProtobufSyncMessageProvider\\Tests\\', __DIR__ . '/tests');
$loader->addPsr4('', __DIR__ . '/../library/src');
