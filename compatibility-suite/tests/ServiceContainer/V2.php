<?php

namespace PhpPactTest\CompatibilitySuite\ServiceContainer;

class V2 extends V1
{
    protected function getSpecification(): string
    {
        return '2.0.0';
    }
}
