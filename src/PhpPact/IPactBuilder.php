<?php

namespace PhpPact;

interface IPactBuilder
{
    public function serviceConsumer($consumerName);

    public function hasPactWith($providerName);

    public function setMockService($providerName, \PhpPact\PactConfig $config);

    public function build();
}
