<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Psr7\Uri;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\BodyStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\GeneratorServerInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestGeneratorBuilderInterface;
use PHPUnit\Framework\Assert;

final class RequestGeneratorsContext implements Context
{
    private int $id = 1;
    private PactPath $pactPath;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private RequestGeneratorBuilderInterface $requestGeneratorBuilder,
        private InteractionsStorageInterface $storage,
        private PactWriterInterface $pactWriter,
        private GeneratorServerInterface $generatorServer,
        private ProviderVerifierInterface $providerVerifier,
        private BodyStorageInterface $bodyStorage,
    ) {
        $this->pactPath = new PactPath();
    }

    /**
     * @Given a request configured with the following generators:
     */
    public function aRequestConfiguredWithTheFollowingGenerators(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $interaction = $this->builder->build([
            'No' => $this->id,
            'method' => 'PUT',
            'path' => '/request-generators',
            'body' => $row['body'] ?? '',
        ]);
        $this->requestGeneratorBuilder->build($interaction->getRequest(), $row['generators']);
        $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $this->id, $interaction);
    }

    /**
     * @When the request is prepared for use
     */
    public function theRequestIsPreparedForUse(): void
    {
        $this->generatorServer->start();
        $this->pactWriter->write($this->id, $this->pactPath);
        $this->providerVerifier->getConfig()->getProviderInfo()->setPort($this->generatorServer->getPort());
        $this->providerVerifier->addSource($this->pactPath);
        $this->providerVerifier->verify();
        $this->generatorServer->stop();
        $this->bodyStorage->setBody($this->generatorServer->getBody());
    }

    /**
     * @Given the generator test mode is set as :mode
     */
    public function theGeneratorTestModeIsSetAs(string $mode): void
    {
        // There is nothing we can do using FFI call.
    }

    /**
     * @When the request is prepared for use with a "providerState" context:
     */
    public function theRequestIsPreparedForUseWithAProviderStateContext(TableNode $table): void
    {
        $this->generatorServer->start();
        $this->pactWriter->write($this->id, $this->pactPath);
        $port = $this->generatorServer->getPort();
        $this->providerVerifier->getConfig()->getProviderInfo()->setPort($port);
        $params = json_decode($table->getRow(0)[0], true);
        $this->providerVerifier
            ->getConfig()
                ->getProviderState()
                    ->setStateChangeUrl(new Uri("http://localhost:$port/return-provider-state-values?" . http_build_query($params)))
                    ->setStateChangeTeardown(false);
        $this->providerVerifier->addSource($this->pactPath);
        $this->providerVerifier->verify();
        $this->generatorServer->stop();
        $this->bodyStorage->setBody($this->generatorServer->getBody());
    }

    /**
     * @Then the request :part will be set as :value
     */
    public function theRequestWillBeSetAs(string $part, string $value): void
    {
        switch ($part) {
            case 'path':
                $path = $this->generatorServer->getPath();
                Assert::assertSame($value, $path);
                break;

            default:
                break;
        }
    }

    /**
     * @Then the request :part will match :regex
     */
    public function theRequestWillMatch(string $part, string $regex): void
    {
        if ($part === 'path') {
            Assert::assertMatchesRegularExpression("/$regex/", $this->generatorServer->getPath());
        } elseif (preg_match('/header\[(.*)\]/', $part, $matches)) {
            foreach ($this->generatorServer->getHeader($matches[1]) as $value) {
                Assert::assertMatchesRegularExpression("/$regex/", $value);
            }
        } elseif (preg_match('/queryParameter\[(.*)\]/', $part, $matches)) {
            Assert::assertMatchesRegularExpression("/$regex/", $this->generatorServer->getQueryParam($matches[1]));
        }
    }
}
