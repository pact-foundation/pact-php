<?php

namespace MessageConsumer;

class ExampleMessageConsumer
{
    public function processMessage(string $message): void
    {
        $obj = \json_decode($message);
        if (($logLevel = \getenv('PACT_LOGLEVEL')) && !in_array(\strtoupper($logLevel), ['OFF', 'NONE'])) {
            print " [x] Processing \n";
            print " [x] Contents: \n";
            print ' [x]     Text: ' . \print_r($obj->contents->text, true) . "\n";
            print ' [x]     Number: ' . \print_r($obj->contents->number, true) . "\n";
            print " [x] Metadata: \n";
            print ' [x]     Queue: ' . \print_r($obj->metadata->queue, true) . "\n";
            print ' [x]     Routing Key: ' . \print_r($obj->metadata->routing_key, true) . "\n";
            print " [x] Processed \n";
        }
    }
}
