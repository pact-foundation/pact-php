<?php

namespace ProtobufAsyncMessageConsumer\Service;

class SayHelloService
{
    public function sayHello(string $given, string $surname): void
    {
        print "Hello {$given} {$surname}";
    }
}
