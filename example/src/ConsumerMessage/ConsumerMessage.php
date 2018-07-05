<?php

class ConsumerMessage
{
    public function Process(string $message) {
        echo " [x] Processed ", $message , "\n";
    }
}