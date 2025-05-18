<?php

require __DIR__.'/../autoload.php';

use ProtobufSyncMessageProvider\Service\Calculator;

$port = 50051;
$server = new \Grpc\RpcServer();
$server->addHttp2Port('0.0.0.0:'.$port);
$server->handle(new Calculator());
echo 'Listening on port :' . $port . PHP_EOL;
$server->run();
