<?php

namespace MessageConsumer;

class ExampleMessageConsumer
{
    public function ProcessText($message)
    {
        $obj = \json_decode($message);
        print ' [x] Processed ' . \print_r($obj->contents->text, true) . "\n";

        return $obj;
    }

    public function ProcessSong($message)
    {
        $obj = \json_decode($message);
        print ' [x] Processed ' . \print_r($obj->contents->song, true) . "\n";

        return $obj;
    }
}
