<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Service\BodyStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\ClientInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\ResponseGeneratorBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;

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
     * @When the request is prepared for use with a "mockServer" context:
     */
    public function theRequestIsPreparedForUseWithAMockServerContext(TableNode $table): void
    {
        $interaction = $this->builder->build([
            'No' => $this->id,
            'method' => 'GET',
            'path' => '/response-generators',
            'response body' => 'file: basic.json',
        ]);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->storage->add(InteractionsStorageInterface::CLIENT_DOMAIN, $this->id, $interaction, true);
        $this->responseGeneratorBuilder->build($interaction->getResponse(), 'mockserver-generator.json');

        $this->server->register($this->id);
        $this->client->sendRequestToServer($this->id);

        $body = $this->client->getResponse()->getBody()->getContents();
        $href = json_decode($table->getRow(0)[0], true)['href'];
        $serverBaseUri = $this->server->getBaseUri();
        $search = [
            (string) $serverBaseUri->withHost('127.0.0.1'),
            (string) $serverBaseUri->withHost('::1'),
            (string) $serverBaseUri->withHost('[::1]'),
            (string) $serverBaseUri->withHost('0:0:0:0:0:0:0:1'),
        ];
        $body = str_replace($search, $href, $body);
        $this->bodyStorage->setBody($body);
    }
}
