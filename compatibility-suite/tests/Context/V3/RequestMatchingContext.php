<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Exception\IntegrationJsonFormatException;
use PhpPactTest\CompatibilitySuite\Service\ClientInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestMatchingRuleBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class RequestMatchingContext implements Context
{
    public const HEADER_TYPE = 'header';
    public const BODY_TYPE = 'body';

    private int $id = 1;
    private string $type;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private ServerInterface $server,
        private ClientInterface $client,
        private InteractionsStorageInterface $storage,
        private RequestBuilderInterface $requestBuilder,
        private RequestMatchingRuleBuilderInterface $requestMatchingRuleBuilder,
    ) {
    }

    /**
     * @Given an expected request with a(n) :header header of :value
     */
    public function anExpectedRequestWithAHeaderOf(string $header, string $value): void
    {
        $this->type = self::HEADER_TYPE;
        $interaction = $this->builder->build([
            'No' => $this->id,
            'method' => 'GET',
            'path' => '/matching',
            'headers' => implode(', ', array_map(fn (string $value) => "'$header: $value'", explode(', ', $value))),
        ]);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->storage->add(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id, $interaction, true);
        $this->server->register($this->id);
    }

    /**
     * @Given a request is received with a(n) :header header of :value
     */
    public function aRequestIsReceivedWithAHeaderOf(string $header, string $value): void
    {
        $request = $this->storage->get(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id)->getRequest();
        $request->addHeader($header, $value);
        $this->client->sendRequestToServer($this->id);
    }

    /**
     * @When the request is compared to the expected one
     */
    public function theRequestIsComparedToTheExpectedOne(): void
    {
        $this->server->verify();
    }

    /**
     * @Then the comparison should be OK
     */
    public function theComparisonShouldBeOk(): void
    {
        Assert::assertTrue($this->server->getVerifyResult()->isSuccess());
    }

    /**
     * @Then the comparison should NOT be OK
     */
    public function theComparisonShouldNotBeOk(): void
    {
        Assert::assertFalse($this->server->getVerifyResult()->isSuccess());
    }

    /**
     * @Then /^the mismatches will contain a mismatch with error "([^"]+)" -> "(.+)"$/
     */
    public function theMismatchesWillContainAMismatchWithError(string $path, string $error): void
    {
        $error = str_replace('\"', '"', $error);
        $key = $this->type === self::HEADER_TYPE ? 'key' : 'path';
        $mismatches = json_decode($this->server->getVerifyResult()->getOutput(), true);
        $mismatches = array_reduce($mismatches, function (array $results, array $mismatch): array {
            Assert::assertSame('request-mismatch', $mismatch['type']);
            $results = array_merge($results, array_filter(
                $mismatch['mismatches'],
                fn (array $mismatch) => $mismatch['type'] === Mismatch::MOCK_SERVER_MISMATCH_TYPE_MAP[$this->type]
            ));

            return $results;
        }, []);
        $mismatches = array_filter(
            $mismatches,
            fn (array $mismatch) => $mismatch[$key] === $path
                && (
                    str_contains($mismatch['mismatch'], $error)
                    || @preg_match("|$error|", $mismatch['mismatch'])
                )
        );
        Assert::assertNotEmpty($mismatches);
    }

    /**
     * @Given an expected request configured with the following:
     */
    public function anExpectedRequestConfiguredWithTheFollowing(TableNode $table): void
    {
        $this->type = self::BODY_TYPE;
        $rows = $table->getHash();
        $row = reset($rows);
        $interaction = $this->builder->build([
            'No' => $this->id,
            'method' => 'POST',
            'path' => '/matching',
        ] + $row);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->storage->add(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id, $interaction, true);
        try {
            $this->requestMatchingRuleBuilder->build($interaction->getRequest(), $row['matching rules']);
        } catch (IntegrationJsonFormatException $exception) {
            throw new PendingException($exception->getMessage());
        }
        $this->server->register($this->id);
    }

    /**
     * @Given a request is received with the following:
     */
    public function aRequestIsReceivedWithTheFollowing(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $request = $this->storage->get(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id)->getRequest();
        $this->requestBuilder->build($request, $row);
        $this->client->sendRequestToServer($this->id);
    }

    /**
     * @Given the following requests are received:
     */
    public function theFollowingRequestsAreReceived(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $request = $this->storage->get(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id)->getRequest();
            $this->requestBuilder->build($request, $row);
            $this->client->sendRequestToServer($this->id);
        }
    }

    /**
     * @When the requests are compared to the expected one
     */
    public function theRequestsAreComparedToTheExpectedOne(): void
    {
        $this->server->verify();
    }
}
