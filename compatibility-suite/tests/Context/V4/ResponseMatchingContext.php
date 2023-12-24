<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PhpPactTest\CompatibilitySuite\Service\ResponseMatchingRuleBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class ResponseMatchingContext implements Context
{
    private int $id = 1;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private InteractionsStorageInterface $storage,
        private ResponseMatchingRuleBuilderInterface $responseMatchingRuleBuilder,
        private ServerInterface $server,
        private PactWriterInterface $pactWriter,
        private ProviderVerifierInterface $providerVerifier,
    ) {
    }

    /**
     * @Given an expected response configured with the following:
     */
    public function anExpectedResponseConfiguredWithTheFollowing(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $interaction = $this->builder->build([
            'No' => $this->id,
            'method' => 'GET',
            'path' => '/matching',
        ] + $row);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $this->id, $interaction);
        $this->responseMatchingRuleBuilder->build($interaction->getResponse(), $row['matching rules']);
        $pactPath = new PactPath();
        $this->pactWriter->write($this->id, $pactPath);
        $this->providerVerifier->addSource($pactPath);
    }

    /**
     * @Given a status :status response is received
     */
    public function aStatusResponseIsReceived(int $status): void
    {
        $interaction = $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $this->id);
        $interaction->getResponse()->setStatus($status);
        $this->server->register($this->id);
    }

    /**
     * @When the response is compared to the expected one
     */
    public function theResponseIsComparedToTheExpectedOne(): void
    {
        $this->providerVerifier->getConfig()->getProviderInfo()->setPort($this->server->getPort());
        $this->providerVerifier->verify();
    }

    /**
     * @Then the response comparison should be OK
     */
    public function theResponseComparisonShouldBeOk(): void
    {
        Assert::assertTrue($this->providerVerifier->getVerifyResult()->isSuccess());
    }

    /**
     * @Then the response comparison should NOT be OK
     */
    public function theResponseComparisonShouldNotBeOk(): void
    {
        Assert::assertFalse($this->providerVerifier->getVerifyResult()->isSuccess());
    }

    /**
     * @Then the response mismatches will contain a :type mismatch with error :error
     */
    public function theResponseMismatchesWillContainAMismatchWithError(string $type, string $error): void
    {
        $output = json_decode($this->providerVerifier->getVerifyResult()->getOutput(), true);
        $errors = array_reduce(
            $output['errors'],
            function (array $errors, array $error) use ($type) {
                switch ($error['mismatch']['type']) {
                    case 'mismatches':
                        foreach ($error['mismatch']['mismatches'] as $mismatch) {
                            if ($mismatch['type'] === Mismatch::MOCK_SERVER_MISMATCH_TYPE_MAP[$type]) {
                                $errors[] = $mismatch['mismatch'];
                            }
                        }
                        break;

                    default:
                        break;
                }

                return $errors;
            },
            []
        );
        Assert::assertContains($error, $errors);
    }
}
