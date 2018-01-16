<?php

namespace PhpPact\Mocks;

interface MockProviderInterface
{
    public function given($providerState);

    public function uponReceiving($description);
}
