<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/MessageConsumer.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel    = $connection->channel();

// the queue should be part of the Pact metadata
$channel->queue_declare('myQueue', false, false, false, false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function ($msg) {

    // process that invokes the use of the message
    $processor = new MessageConsumer\MessageConsumer();
    $processor->Process($msg->body);

    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume('myQueue', '', false, false, false, false, $callback);
while (\count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();