<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface HttpClientInterface
{
    public function sendRequest(ConsumerRequest $request, UriInterface $uri): ResponseInterface;
}
