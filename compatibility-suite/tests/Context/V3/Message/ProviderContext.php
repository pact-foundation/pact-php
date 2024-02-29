<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3\Message;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Gherkin\Node\TableNode;
use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ParserInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;

final class ProviderContext implements Context
{
    private int $id = 1;
    private array $ids = [];
    private PactPath $pactPath;

    public function __construct(
        private ServerInterface $server,
        private InteractionBuilderInterface $builder,
        private InteractionsStorageInterface $storage,
        private MessagePactWriterInterface $pactWriter,
        private ProviderVerifierInterface $providerVerifier,
        private ParserInterface $parser,
        private FixtureLoaderInterface $fixtureLoader
    ) {
        $this->pactPath = new PactPath();
    }

    /**
     * @Given /^a provider is started that can generate the "([^"]+)" message with "(.+)"$/
     */
    public function aProviderIsStartedThatCanGenerateTheMessageWith(string $name, string $fixture): void
    {
        $fixture = str_replace('\"', '"', $fixture);
        $interaction = $this->builder->build([
            'No' => $this->id,
            'description' => sprintf('Interaction for message %s', $name),
            'method' => 'POST',
            'path' => '/messages',
            'body' => 'JSON: ' . json_encode(['description' => $name]),
            'response body' => $fixture,
        ]);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->ids[] = $this->id++;
    }

    /**
     * @BeforeStep
     */
    public function registerInteractions(BeforeStepScope $scope): void
    {
        if (
            $this->ids
            && preg_match('/^a Pact file for .* is to be verified/', $scope->getStep()->getText())
        ) {
            $this->server->register(...$this->ids);
            $this->ids = [];
            $this->providerVerifier
                ->getConfig()
                    ->addProviderTransport(
                        (new ProviderTransport())
                            ->setProtocol(ProviderTransport::MESSAGE_PROTOCOL)
                            ->setPort($this->server->getPort())
                            ->setPath('/messages')
                            ->setScheme('http')
                    );
            ;
        }
    }

    /**
     * @Given a Pact file for :name::fixture is to be verified
     */
    public function aPactFileForIsToBeVerified(string $name, string $fixture): void
    {
        $message = new Message();
        $message->setDescription($name);
        $message->setContents($this->parser->parseBody($fixture));
        $this->pactWriter->write($message, $this->pactPath);
        $this->providerVerifier->addSource($this->pactPath);
    }

    /**
     * @Given a Pact file for :name::fixture is to be verified with provider state :state
     */
    public function aPactFileForIsToBeVerifiedWithProviderState(string $name, string $fixture, string $state): void
    {
        $this->aPactFileForIsToBeVerified($name, $fixture);
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $pact['messages'][0]['providerState'] = $state;
        file_put_contents($this->pactPath, json_encode($pact));
    }

    /**
     * @Given a provider is started that can generate the :name message with :fixture and the following metadata:
     */
    public function aProviderIsStartedThatCanGenerateTheMessageWithAndTheFollowingMetadata(string $name, string $fixture, TableNode $table): void
    {
        $interaction = $this->builder->build([
            'No' => $this->id,
            'description' => sprintf('Interaction for message %s', $name),
            'method' => 'POST',
            'path' => '/messages',
            'body' => 'JSON: ' . json_encode(['description' => $name]),
            'response body' => $fixture,
            'response headers' => 'Pact-Message-Metadata: ' . base64_encode(json_encode($this->parser->parseMetadataTable($table->getHash()))),
        ]);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->server->register($this->id);
        $this->providerVerifier
            ->getConfig()
                ->addProviderTransport(
                    (new ProviderTransport())
                        ->setProtocol(ProviderTransport::MESSAGE_PROTOCOL)
                        ->setPort($this->server->getPort())
                        ->setPath('/messages')
                        ->setScheme('http')
                );
        ;
    }

    /**
     * @Given a Pact file for :name::fixture is to be verified with the following metadata:
     */
    public function aPactFileForIsToBeVerifiedWithTheFollowingMetadata(string $name, string $fixture, TableNode $table): void
    {
        $this->aPactFileForIsToBeVerified($name, $fixture);
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $pact['messages'][0]['metaData'] = $this->parser->parseMetadataTable($table->getHash());
        file_put_contents($this->pactPath, json_encode($pact));
    }

    /**
     * @Given a Pact file for :name is to be verified with the following:
     */
    public function aPactFileForIsToBeVerifiedWithTheFollowing(string $name, TableNode $table): void
    {
        foreach ($table->getRowsHash() as $key => $value) {
            switch ($key) {
                case 'body':
                    $body = $value;
                    break;

                case 'matching rules':
                    $matchingRules = $this->fixtureLoader->loadJson($value);
                    break;

                case 'metadata':
                    $metadata = $value;
                    break;

                default:
                    break;
            }
        }
        $this->aPactFileForIsToBeVerified($name, $body);
        $pact = json_decode(file_get_contents($this->pactPath), true);
        if (isset($metadata)) {
            $pact['messages'][0]['metadata'] = array_merge($pact['messages'][0]['metadata'], $this->parser->parseMetadataMultiValues($metadata));
        }
        $pact['messages'][0]['matchingRules'] = $matchingRules;
        file_put_contents($this->pactPath, json_encode($pact));
    }
}
