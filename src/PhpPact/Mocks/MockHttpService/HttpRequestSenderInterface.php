<?php

namespace PhpPact\Mocks\MockHttpService;

interface HttpRequestSenderInterface
{
    public function Send($request, $baseUri);
}
