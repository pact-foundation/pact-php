<?php

namespace ConsumerMessage;

class ConsumerMessage
{
    public function Process($message)
    {
        $obj = \json_decode($message);
        print ' [x] Processed ' . $obj->content . "\n";

        return $obj;
    }
}
