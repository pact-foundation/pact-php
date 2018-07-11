<?php

namespace MessageConsumer;

class MessageConsumer
{
    public function Process($message)
    {
        $obj = \json_decode($message);
        print ' [x] Processed ' . \print_r($obj->contents->test, true) . "\n";

        return $obj;
    }

    public function ProcessAnotherMessageType($message)
    {
        $obj = \json_decode($message);
        print ' [x] Processed ' . \print_r($obj->contents->song, true) . "\n";

        return $obj;
    }

}
