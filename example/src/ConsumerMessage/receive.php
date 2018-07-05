<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/ConsumerMessage.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg) {
    $processor = new ConsumerMessage();
    $processor->Process($msg->body);

    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume('hello', '', false, false, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();