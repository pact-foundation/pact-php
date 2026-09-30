<?php

namespace ProtobufAsyncMessageConsumer\MessageHandler;

use Library\Person;
use ProtobufAsyncMessageConsumer\Service\SayHelloService;

class PersonMessageHandler
{
    public function __construct(private SayHelloService $service)
    {
    }

    public function __invoke(Person $person): void
    {
        $name = $person->getName();
        $this->service->sayHello($name?->getGiven() ?? '', $name?->getSurname() ?? '');
    }
}
