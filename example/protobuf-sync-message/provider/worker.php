<?php

use ProtobufSyncMessageProvider\Service\Calculator;
use ProtobufSyncMessageProvider\Service\CalculatorInterface;
use Spiral\RoadRunner\GRPC\Invoker;
use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\Worker;

require __DIR__ . '/autoload.php';

$server = new Server(new Invoker(), [
    'debug' => false, // optional (default: false)
]);

$server->registerService(CalculatorInterface::class, new Calculator());

$server->serve(Worker::create());
