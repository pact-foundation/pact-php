<?php

namespace PhpPact\Mocks;

interface IMockProvider
{
    public function Given($providerState);

    public function UponReceiving($description);
}
