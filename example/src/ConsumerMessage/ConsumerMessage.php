<?php

class ConsumerMessage
{
    public function Process($message) {
        $obj = \json_decode($message);
        echo " [x] Processed ".  $obj->content . "\n";

        return $obj;
    }
}