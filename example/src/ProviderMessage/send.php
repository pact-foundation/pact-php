<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/ProviderMessage.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

$providerMessage = new ProviderMessage(['queue'=>'myQueue', 'routing_key'=>'myQueue']);

$channel = $connection->channel();
$channel->queue_declare($providerMessage->getMetadata()['queue'], false, false, false, false);

$msg = new AMQPMessage($providerMessage->Publish('Wind cries, Mary'));

$channel->basic_publish($msg, '', $providerMessage->getMetadata()['routing_key']);

$channel->close();
$connection->close();
