<?php

namespace MessageConsumer;

class ExampleMessageConsumer
{
    public function processMessage(string $message): void
    {
        $obj = \json_decode($message);
        print " [x] Processing \n";
        print ' [x] Text: ' . \print_r($obj->contents->text, true) . "\n";
        print ' [x] Number: ' . \print_r($obj->contents->number, true) . "\n";
        print " [x] Processed \n";
    }
}
