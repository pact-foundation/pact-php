<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/ProviderMessage.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);

$providerMessage = new ProviderMessage();
$msg = new AMQPMessage($providerMessage->Publish());

$channel->basic_publish($msg, '', 'hello');

$channel->close();
$connection->close();