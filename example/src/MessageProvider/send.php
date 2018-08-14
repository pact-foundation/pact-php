<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/ExampleMessageProvider.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

// build the message with appropriate metadata
$providerMessage = new \MessageProvider\ExampleMessageProvider(['queue'=>'myKey', 'routing_key'=>'myKey']);
$content         = new \stdClass();
$content->text   = 'Hello Mary';
$providerMessage->setContents($content);

$channel = $connection->channel();
$channel->queue_declare($providerMessage->getMetadata()['queue'], false, false, false, false);

// transform message to AMQP
$msg = new AMQPMessage($providerMessage->Build());

// publish it
$channel->basic_publish($msg, '', $providerMessage->getMetadata()['routing_key']);

$channel->close();
$connection->close();
