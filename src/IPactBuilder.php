<?php
namespace PhpPact;

interface IPactBuilder
{
    public function ServiceConsumer($consumerName);

    public function HasPactWith($providerName);

    public function setMockService($providerName, \PhpPact\PactConfig $config);

    public function Build();
}
