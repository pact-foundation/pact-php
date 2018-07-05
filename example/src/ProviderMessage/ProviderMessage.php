<?php

class ProviderMessage
{
    // perhaps build a json object, etc
    public function Publish() {
        $message = 'Hello World';

        echo " [x] Publishing ", $message , "\n";
        return $message;
    }
}