<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Service\BodyStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\ClientInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\ResponseGeneratorBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class ResponseGeneratorsContext implements Context
{
    private int $id = 1;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private ResponseGeneratorBuilderInterface $responseGeneratorBuilder,
        private InteractionsStorageInterface $storage,
        private ServerInterface $server,
        private ClientInterface $client,
        private BodyStorageInterface $bodyStorage,
    ) {
    }

    /**
     * @Given a response configured with the following generators:
     */
    public function aResponseConfiguredWithTheFollowingGenerators(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $interaction = $this->builder->build([
            'No' => $this->id,
            'method' => 'GET',
            'path' => '/response-generators',
            'response body' => $row['body'] ?? '',
        ]);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->storage->add(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id, $interaction, true);
        $this->responseGeneratorBuilder->build($interaction->getResponse(), $row['generators']);
    }

    /**
     * @When the response is prepared for use
     */
    public function theResponseIsPreparedForUse(): void
    {
        $this->server->register($this->id);
        $this->client->sendRequestToServer($this->id);
        $this->bodyStorage->setBody($this->client->getResponse()->getBody()->getContents());
    }

    /**
     * @Then the response :part will not be :value
     */
    public function theResponseWillNotBe(string $part, string $value): void
    {
        switch ($part) {
            case 'status':
                $code = $this->client->getResponse()->getStatusCode();
                Assert::assertNotEquals($value, $code);
                break;

            default:
                break;
        }
    }

    /**
     * @Then the response :part will match :regex
     */
    public function theResponseWillMatch(string $part, string $regex): void
    {
        if ($part === 'status') {
            Assert::assertMatchesRegularExpression("/$regex/", $this->client->getResponse()->getStatusCode());
        } elseif (preg_match('/header\[(.*)\]/', $part, $matches)) {
            foreach ($this->client->getResponse()->getHeader($matches[1]) as $value) {
                Assert::assertMatchesRegularExpression("/$regex/", $value);
            }
        }
    }
}
