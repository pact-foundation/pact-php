<?php

namespace PhpPactTest\CompatibilitySuite\Context\V1\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Then;
use Behat\Step\When;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Service\ClientInterface;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PhpPactTest\CompatibilitySuite\Util\Arr;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    /**
     * @var array<array-key, mixed>
     */
    private array $pact;

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
        $this->pact = (array) json_decode((string) file_get_contents($this->server->getPactPath()), true);
        Assert::assertEquals($num, count(Arr::sub($this->pact, 'interactions')));
    }

    #[Then('the {first} interaction request will be for a :method')]
    public function theFirstInteractionRequestWillBeForA(string $method): void
    {
        Assert::assertSame($method, Arr::str($this->pact, 'interactions', 0, 'request', 'method'));
    }

    #[Then('the {first} interaction response will contain the :fixture document')]
    public function theFirstInteractionResponseWillContainTheDocument(string $fixture): void
    {
        Assert::assertEquals($this->fixtureLoader->loadJson($fixture), Arr::sub($this->pact, 'interactions', 0, 'response', 'body'));
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
        $mismatch = (array) current($mismatches);
        Assert::assertSame('missing-request', Arr::str($mismatch, 'type'));
        Assert::assertSame($request->getMethod(), Arr::str($mismatch, 'request', 'method'));
        Assert::assertSame($request->getPath(), Arr::str($mismatch, 'request', 'path'));
        Assert::assertSame($request->getQuery(), Arr::sub($mismatch, 'request', 'query'));
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
        $notFoundRequests = array_filter($mismatches, fn (mixed $mismatch) => Arr::str((array) $mismatch, 'type') === 'request-not-found');
        $mismatch = (array) current($notFoundRequests);
        Assert::assertSame($request->getMethod(), Arr::str($mismatch, 'request', 'method'));
        Assert::assertSame($request->getPath(), Arr::str($mismatch, 'request', 'path'));
        // TODO assert query, headers, body
    }

    #[Then('the {first} interaction request query parameters will be :query')]
    public function theFirstInteractionRequestQueryParametersWillBe(string $query): void
    {
        Assert::assertEquals($query, Arr::str($this->pact, 'interactions', 0, 'request', 'query'));
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
        $mismatch = (array) current($mismatches);
        Assert::assertSame('request-mismatch', Arr::str($mismatch, 'type'));
        $mismatches = array_filter(
            Arr::sub($mismatch, 'mismatches'),
            fn (mixed $mismatch) => Arr::str((array) $mismatch, 'type') === Mismatch::MOCK_SERVER_MISMATCH_TYPE_MAP[$type]
                && str_contains(Arr::str((array) $mismatch, 'mismatch'), $error)
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
        $notFoundRequests = array_filter($mismatches, fn (mixed $mismatch) => Arr::str((array) $mismatch, 'type') === 'request-not-found');
        $mismatch = (array) current($notFoundRequests);
        Assert::assertSame($method, Arr::str($mismatch, 'request', 'method'));
        Assert::assertSame($path, Arr::str($mismatch, 'request', 'path'));
    }

    #[Then('the {first} interaction request will contain the header :header with value :value')]
    public function theFirstInteractionRequestWillContainTheHeaderWithValue(string $header, string $value): void
    {
        Assert::assertArrayHasKey($header, Arr::sub($this->pact, 'interactions', 0, 'request', 'headers'));
        Assert::assertSame($value, Arr::str($this->pact, 'interactions', 0, 'request', 'headers', $header));
    }

    #[Then('the {first} interaction request content type will be :contentType')]
    public function theFirstInteractionRequestContentTypeWillBe(string $contentType): void
    {
        Assert::assertSame($contentType, Arr::str($this->pact, 'interactions', 0, 'request', 'headers', 'Content-Type'));
    }

    #[Then('the {first} interaction request will contain the :fixture document')]
    public function theFirstInteractionRequestWillContainTheDocument(string $fixture): void
    {
        Assert::assertEquals($this->fixtureLoader->loadJson($fixture), Arr::sub($this->pact, 'interactions', 0, 'request', 'body'));
    }

    #[Then('the mismatches will contain a :type mismatch with path :path with error :error')]
    public function theMismatchesWillContainAMismatchWithPathWithError(string $type, string $path, string $error): void
    {
        $mismatches = $this->getMismatches();
        $mismatch = (array) current($mismatches);
        Assert::assertSame('request-mismatch', Arr::str($mismatch, 'type'));
        $mismatches = array_filter(
            Arr::sub($mismatch, 'mismatches'),
            fn (mixed $mismatch) => Arr::str((array) $mismatch, 'type') === Mismatch::MOCK_SERVER_MISMATCH_TYPE_MAP[$type]
                && Arr::str((array) $mismatch, 'path') === $path
                && str_contains(Arr::str((array) $mismatch, 'mismatch'), $error)
        );
        Assert::assertNotEmpty($mismatches);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getMismatches(): array
    {
        if ($this->server->getVerifyResult()->isSuccess()) {
            return [];
        }

        return (array) json_decode($this->server->getVerifyResult()->getOutput(), true);
    }
}
