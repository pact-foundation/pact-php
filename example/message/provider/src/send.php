<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use MessageProvider\ExampleMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

// build the message with appropriate metadata
$content         = new \stdClass();
$content->text   = 'Hello Mary';
$metadata = ['queue' => 'myKey', 'routing_key' => 'myKey'];
$providerMessage = new ExampleMessage($content, $metadata);

$channel = $connection->channel();
$channel->queue_declare($providerMessage->getMetadata()['queue'], false, false, false, false);

// transform message to AMQP
$msg = new AMQPMessage($providerMessage);

// publish it
$channel->basic_publish($msg, '', $providerMessage->getMetadata()['routing_key']);

$channel->close();
$connection->close();
