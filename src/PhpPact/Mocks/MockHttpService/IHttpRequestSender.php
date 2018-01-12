<?php

namespace PhpPact\Mocks\MockHttpService;

interface IHttpRequestSender
{
    public function Send($request, $baseUri);
}
