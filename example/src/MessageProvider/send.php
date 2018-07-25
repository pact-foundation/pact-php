<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/ExampleMessageProvider.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

$providerMessage = new MessageProvider(['queue'=>'myQueue', 'routing_key'=>'myQueue']);

$channel = $connection->channel();
$channel->queue_declare($providerMessage->getMetadata()['queue'], false, false, false, false);

$channel->exchange_declare('DougExchange', 'direct');
$channel->queue_bind($providerMessage->getMetadata()['queue'], 'DougExchange', $providerMessage->getMetadata()['routing_key']);

$msg = new AMQPMessage($providerMessage->Publish());

$channel->basic_publish($msg, 'DougExchange');

$channel->close();
$connection->close();
