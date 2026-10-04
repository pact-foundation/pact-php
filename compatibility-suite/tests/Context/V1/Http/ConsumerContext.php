<?php

namespace PhpPactTest\CompatibilitySuite\Context\V1\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Then;
use Behat\Step\When;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Model\Mismatch\Mismatch as DatatypeMismatch;
use PhpPactTest\CompatibilitySuite\Model\MockServer\RequestMismatch;
use PhpPactTest\CompatibilitySuite\Model\Pact\Pact;
use PhpPactTest\CompatibilitySuite\Service\ClientInterface;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private Pact $pact;

    public function __construct(
        private ServerInterface $server,
        private RequestBuilderInterface $requestBuilder,
        private ClientInterface $client,
        private InteractionsStorageInterface $storage,
        private FixtureLoaderInterface $fixtureLoader,
    ) {
    }

    #[When('the mock server is started with interaction :id')]
    public function theMockServerIsStartedWithInteraction(int $id): void
    {
        $this->server->register($id);
    }

    #[When('request :id is made to the mock server')]
    public function requestIsMadeToTheMockServer(int $id): void
    {
        $this->client->sendRequestToServer($id);
    }

    #[Then('a :code success response is returned')]
    public function aSuccessResponseIsReturned(int $code): void
    {
        Assert::assertSame($code, $this->client->getResponse()->getStatusCode());
    }

    #[Then('the payload will contain the :name JSON document')]
    public function thePayloadWillContainTheJsonDocument(string $name): void
    {
        Assert::assertJsonStringEqualsJsonString($this->fixtureLoader->load($name . '.json'), (string) $this->client->getResponse()->getBody());
    }

    #[Then('the content type will be set as :contentType')]
    public function theContentTypeWillBeSetAs(string $contentType): void
    {
        Assert::assertSame($contentType, $this->client->getResponse()->getHeaderLine('Content-Type'));
    }

    #[When('the pact test is done')]
    public function thePactTestIsDone(): void
    {
        $this->server->verify();
    }

    #[Then('the mock server status will be OK')]
    public function theMockServerStatusWillBeOk(): void
    {
        Assert::assertTrue($this->server->getVerifyResult()->isSuccess());
    }

    #[Then('the mock server will write out a Pact file for the interaction when done')]
    public function theMockServerWillWriteOutAPactFileForTheInteractionWhenDone(): void
    {
        Assert::assertTrue(file_exists($this->server->getPactPath()));
    }

    #[Then('the pact file will contain {:num} interaction(s)')]
    public function thePactFileWillContainInteraction(int $num): void
    {
        $this->pact = Pact::fromFile($this->server->getPactPath());
        Assert::assertEquals($num, count($this->pact->getInteractions()));
    }

    #[Then('the {first} interaction request will be for a :method')]
    public function theFirstInteractionRequestWillBeForA(string $method): void
    {
        Assert::assertSame($method, $this->pact->getInteraction(0)->getRequest()->getMethod());
    }

    #[Then('the {first} interaction response will contain the :fixture document')]
    public function theFirstInteractionResponseWillContainTheDocument(string $fixture): void
    {
        Assert::assertEquals($this->fixtureLoader->loadJson($fixture), $this->pact->getInteraction(0)->getResponse()->getBody());
    }

    #[When('the mock server is started with interactions :ids')]
    public function theMockServerIsStartedWithInteractions(string $ids): void
    {
        $ids = array_map(fn (string $id) => (int) trim($id), explode(',', $ids));
        $this->server->register(...$ids);
    }

    #[Then('the mock server status will NOT be OK')]
    public function theMockServerStatusWillNotBeOk(): void
    {
        Assert::assertFalse($this->server->getVerifyResult()->isSuccess());
    }

    #[Then('the mock server will NOT write out a Pact file for the interactions when done')]
    public function theMockServerWillNotWriteOutAPactFileForTheInteractionsWhenDone(): void
    {
        Assert::assertFileDoesNotExist($this->server->getPactPath());
    }

    #[Then('the mock server status will be an expected but not received error for interaction {:id}')]
    public function theMockServerStatusWillBeAnExpectedButNotReceivedErrorForInteraction(int $id): void
    {
        $request = $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $id)->getRequest();
        $mismatches = $this->getMismatches();
        Assert::assertCount(1, $mismatches);
        $mismatch = reset($mismatches);
        Assert::assertInstanceOf(RequestMismatch::class, $mismatch);
        Assert::assertSame(RequestMismatch::TYPE_MISSING_REQUEST, $mismatch->getType());
        Assert::assertSame($request->getMethod(), $mismatch->getRequest()->getMethod());
        Assert::assertSame($request->getPath(), $mismatch->getRequest()->getPath());
        Assert::assertSame($request->getQuery(), $mismatch->getRequest()->getQuery());
        // TODO assert headers, body
    }

    #[Then('a :code error response is returned')]
    public function aErrorResponseIsReturned(int $code): void
    {
        Assert::assertSame($code, $this->client->getResponse()->getStatusCode());
    }

    #[Then('the mock server status will be an unexpected :method request received error for interaction {:id}')]
    public function theMockServerStatusWillBeAnUnexpectedRequestReceivedErrorForInteraction(string $method, int $id): void
    {
        $request = $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $id)->getRequest();
        $mismatches = $this->getMismatches();
        Assert::assertCount(2, $mismatches);
        $notFoundRequests = array_filter($mismatches, fn (RequestMismatch $mismatch): bool => $mismatch->getType() === RequestMismatch::TYPE_REQUEST_NOT_FOUND);
        $mismatch = reset($notFoundRequests);
        Assert::assertInstanceOf(RequestMismatch::class, $mismatch);
        Assert::assertSame($request->getMethod(), $mismatch->getRequest()->getMethod());
        Assert::assertSame($request->getPath(), $mismatch->getRequest()->getPath());
        // TODO assert query, headers, body
    }

    #[Then('the {first} interaction request query parameters will be :query')]
    public function theFirstInteractionRequestQueryParametersWillBe(string $query): void
    {
        Assert::assertEquals($query, $this->pact->getInteraction(0)->getRequest()->getQuery());
    }

    #[When('request :id is made to the mock server with the following changes:')]
    public function requestIsMadeToTheMockServerWithTheFollowingChanges(int $id, TableNode $table): void
    {
        $request = $this->storage->get(InteractionsStorageInterface::CLIENT_DOMAIN, $id)->getRequest();
        $this->requestBuilder->build($request, $table->getHash()[0]);
        $this->requestIsMadeToTheMockServer($id);
    }

    #[Then('the mock server status will be mismatches')]
    public function theMockServerStatusWillBeMismatches(): void
    {
        $mismatches = $this->getMismatches();
        Assert::assertNotEmpty($mismatches);
    }

    #[Then('the mismatches will contain a :type mismatch with error :error')]
    public function theMismatchesWillContainAMismatchWithError(string $type, string $error): void
    {
        $mismatches = $this->getMismatches();
        $mismatch = reset($mismatches);
        Assert::assertInstanceOf(RequestMismatch::class, $mismatch);
        Assert::assertSame(RequestMismatch::TYPE_REQUEST_MISMATCH, $mismatch->getType());
        $mismatches = array_filter(
            $mismatch->getMismatches(),
            fn (DatatypeMismatch $mismatch): bool => $mismatch->getType() === Mismatch::MOCK_SERVER_MISMATCH_TYPE_MAP[$type]
                && str_contains($mismatch->getMismatch(), $error)
        );
        Assert::assertNotEmpty($mismatches);
    }

    #[Then('the mock server will NOT write out a Pact file for the interaction when done')]
    public function theMockServerWillNotWriteOutAPactFileForTheInteractionWhenDone(): void
    {
        Assert::assertFileDoesNotExist($this->server->getPactPath());
    }

    #[Then('the mock server status will be an unexpected :method request received error for path :path')]
    public function theMockServerStatusWillBeAnUnexpectedRequestReceivedErrorForPath(string $method, string $path): void
    {
        $mismatches = $this->getMismatches();
        Assert::assertCount(2, $mismatches);
        $notFoundRequests = array_filter($mismatches, fn (RequestMismatch $mismatch): bool => $mismatch->getType() === RequestMismatch::TYPE_REQUEST_NOT_FOUND);
        $mismatch = reset($notFoundRequests);
        Assert::assertInstanceOf(RequestMismatch::class, $mismatch);
        Assert::assertSame($method, $mismatch->getRequest()->getMethod());
        Assert::assertSame($path, $mismatch->getRequest()->getPath());
    }

    #[Then('the {first} interaction request will contain the header :header with value :value')]
    public function theFirstInteractionRequestWillContainTheHeaderWithValue(string $header, string $value): void
    {
        $request = $this->pact->getInteraction(0)->getRequest();
        Assert::assertTrue($request->hasHeader($header));
        Assert::assertSame($value, $request->getHeader($header));
    }

    #[Then('the {first} interaction request content type will be :contentType')]
    public function theFirstInteractionRequestContentTypeWillBe(string $contentType): void
    {
        Assert::assertSame($contentType, $this->pact->getInteraction(0)->getRequest()->getHeader('Content-Type'));
    }

    #[Then('the {first} interaction request will contain the :fixture document')]
    public function theFirstInteractionRequestWillContainTheDocument(string $fixture): void
    {
        Assert::assertEquals($this->fixtureLoader->loadJson($fixture), $this->pact->getInteraction(0)->getRequest()->getBody());
    }

    #[Then('the mismatches will contain a :type mismatch with path :path with error :error')]
    public function theMismatchesWillContainAMismatchWithPathWithError(string $type, string $path, string $error): void
    {
        $mismatches = $this->getMismatches();
        $mismatch = reset($mismatches);
        Assert::assertInstanceOf(RequestMismatch::class, $mismatch);
        Assert::assertSame(RequestMismatch::TYPE_REQUEST_MISMATCH, $mismatch->getType());
        $mismatches = array_filter(
            $mismatch->getMismatches(),
            fn (DatatypeMismatch $mismatch): bool => $mismatch->getType() === Mismatch::MOCK_SERVER_MISMATCH_TYPE_MAP[$type]
                && $mismatch->getPath() === $path
                && str_contains($mismatch->getMismatch(), $error)
        );
        Assert::assertNotEmpty($mismatches);
    }

    /**
     * @return list<RequestMismatch>
     */
    private function getMismatches(): array
    {
        if ($this->server->getVerifyResult()->isSuccess()) {
            return [];
        }

        return RequestMismatch::listFromArray((array) json_decode($this->server->getVerifyResult()->getOutput(), true));
    }
}
