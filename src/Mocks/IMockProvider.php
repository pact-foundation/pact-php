<?php

namespace PhpPact\Mocks;

interface IMockProvider
{
    public function given($providerState);

    public function uponReceiving($description);
}
