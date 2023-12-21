<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    public function sendRequestToServer(int $id): void;

    public function getResponse(): ResponseInterface;
}
