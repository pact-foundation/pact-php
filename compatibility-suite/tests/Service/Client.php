<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use Psr\Http\Message\ResponseInterface;

final class Client implements ClientInterface
{
    private ResponseInterface $response;

    public function __construct(
        private ServerInterface $server,
        private InteractionsStorageInterface $storage,
        private HttpClientInterface $httpClient
    ) {
    }

    public function sendRequestToServer(int $id): void
    {
        $request = $this->storage->get(InteractionsStorageInterface::CLIENT_DOMAIN, $id)->getRequest();
        $this->response = $this->httpClient->sendRequest($request, $this->server->getBaseUri());
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
