<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared;

use Behat\Behat\Context\Context;
use GuzzleHttp\Psr7\Uri;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Service\ProviderStateServerInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class ProviderContext implements Context
{
    public function __construct(
        private ServerInterface $server,
        private ProviderVerifierInterface $providerVerifier,
        private ProviderStateServerInterface $providerStateServer,
    ) {
    }

    /**
     * @When the verification is run
     */
    public function theVerificationIsRun(): void
    {
        $this->providerVerifier->getConfig()->getProviderInfo()->setPort($this->server->getPort());
        $this->providerVerifier->verify();
    }

    /**
     * @Then the verification will be successful
     */
    public function theVerificationWillBeSuccessful(): void
    {
        Assert::assertTrue($this->providerVerifier->getVerifyResult()->isSuccess());
    }

    /**
     * @Then the verification will NOT be successful
     */
    public function theVerificationWillNotBeSuccessful(): void
    {
        Assert::assertFalse($this->providerVerifier->getVerifyResult()->isSuccess());
    }

    /**
     * @Given a provider state callback is configured
     */
    public function aProviderStateCallbackIsConfigured(): void
    {
        $port = $this->providerStateServer->getPort();
        $this->providerVerifier
            ->getConfig()
                ->getProviderState()
                    ->setStateChangeUrl(new Uri("http://localhost:$port/pact-change-state"))
                    ->setStateChangeTeardown(true);
        ;
    }

    /**
     * @Then the provider state callback will be called before the verification is run
     */
    public function theProviderStateCallbackWillBeCalledBeforeTheVerificationIsRun(): void
    {
        Assert::assertTrue($this->providerStateServer->hasAction(ProviderStateServerInterface::ACTION_SETUP));
    }

    /**
     * @Then the provider state callback will receive a setup call with :state as the provider state parameter
     */
    public function theProviderStateCallbackWillReceiveASetupCallWithAsTheProviderStateParameter(string $state): void
    {
        Assert::assertTrue($this->providerStateServer->hasState(ProviderStateServerInterface::ACTION_SETUP, $state));
    }

    /**
     * @Then the provider state callback will be called after the verification is run
     */
    public function theProviderStateCallbackWillBeCalledAfterTheVerificationIsRun(): void
    {
        Assert::assertTrue($this->providerStateServer->hasAction(ProviderStateServerInterface::ACTION_TEARDOWN));
    }

    /**
     * @Then the provider state callback will receive a teardown call :state as the provider state parameter
     */
    public function theProviderStateCallbackWillReceiveATeardownCallAsTheProviderStateParameter(string $state): void
    {
        Assert::assertTrue($this->providerStateServer->hasState(ProviderStateServerInterface::ACTION_TEARDOWN, $state));
    }

    /**
     * @Given a provider state callback is configured, but will return a failure
     */
    public function aProviderStateCallbackIsConfiguredButWillReturnAFailure(): void
    {
        $port = $this->providerStateServer->getPort();
        $this->providerVerifier
            ->getConfig()
                ->getProviderState()
                    ->setStateChangeUrl(new Uri("http://localhost:$port/failed-pact-change-state"))
                    ->setStateChangeTeardown(true);
        ;
    }

    /**
     * @Then the provider state callback will NOT receive a teardown call
     */
    public function theProviderStateCallbackWillNotReceiveATeardownCall(): void
    {
        Assert::assertFalse($this->providerStateServer->hasAction(ProviderStateServerInterface::ACTION_TEARDOWN));
    }

    /**
     * @Then the verification results will contain a :error error
     */
    public function theVerificationResultsWillContainAError(string $error): void
    {
        $output = json_decode($this->providerVerifier->getVerifyResult()->getOutput(), true);
        $errors = array_reduce(
            $output['errors'],
            function (array $errors, array $error) {
                switch ($error['mismatch']['type']) {
                    case 'error':
                        $errors[] = Mismatch::VERIFIER_MISMATCH_ERROR_MAP[$error['mismatch']['message']];
                        break;

                    case 'mismatches':
                        foreach ($error['mismatch']['mismatches'] as $mismatch) {
                            $errors[] = Mismatch::VERIFIER_MISMATCH_TYPE_MAP[$mismatch['type']];
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
