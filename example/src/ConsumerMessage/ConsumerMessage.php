<?php

namespace ConsumerMessage;

class ConsumerMessage
{
    public function Process($message)
    {
        $obj = \json_decode($message);
        print ' [x] Processed ' . \print_r($obj->contents->test, true) . "\n";

        return $obj;
    }
}
