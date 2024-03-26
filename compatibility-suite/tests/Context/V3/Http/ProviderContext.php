<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderStateServerInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PHPUnit\Framework\Assert;

final class ProviderContext implements Context
{
    private PactPath $pactPath;

    public function __construct(
        private PactWriterInterface $pactWriter,
        private ProviderStateServerInterface $providerStateServer,
        private ProviderVerifierInterface $providerVerifier,
    ) {
        $this->pactPath = new PactPath();
    }

    /**
     * @Given a Pact file for interaction :id is to be verified with the following provider states defined:
     */
    public function aPactFileForInteractionIsToBeVerifiedWithTheFollowingProviderStatesDefined(int $id, TableNode $table): void
    {
        $this->pactWriter->write($id, $this->pactPath);
        $pact = json_decode(file_get_contents($this->pactPath));
        $rows = $table->getHash();
        $pact->interactions[0]->providerStates = array_map(fn (array $row): array => ['name' => $row['State Name'], 'params' => json_decode($row['Parameters'] ?? '{}', true)], $rows);
        file_put_contents($this->pactPath, json_encode($pact));
        $this->providerVerifier->addSource($this->pactPath);
    }

    /**
     * @Then the provider state callback will receive a setup call with :state and the following parameters:
     */
    public function theProviderStateCallbackWillReceiveASetupCallWithAndTheFollowingParameters(string $state, TableNode $table): void
    {
        $params = $table->getHash()[0];
        foreach ($params as &$value) {
            $value = trim($value, '"');
        }
        Assert::assertTrue($this->providerStateServer->hasState(ProviderStateServerInterface::ACTION_SETUP, $state, $params));
    }

    /**
     * @Then the provider state callback will receive a teardown call :state and the following parameters:
     */
    public function theProviderStateCallbackWillReceiveATeardownCallAndTheFollowingParameters(string $state, TableNode $table): void
    {
        $params = $table->getHash()[0];
        foreach ($params as &$value) {
            $value = trim($value, '"');
        }
        Assert::assertTrue($this->providerStateServer->hasState(ProviderStateServerInterface::ACTION_TEARDOWN, $state, $params));
    }
}
