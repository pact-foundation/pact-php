<?php

namespace PhpPactTest\CompatibilitySuite\Context\V1\Http;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptions;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\PactBrokerInterface;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PhpPactTest\CompatibilitySuite\Service\ResponseBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class ProviderContext implements Context
{
    public function __construct(
        private ServerInterface $server,
        private PactWriterInterface $pactWriter,
        private PactBrokerInterface $pactBroker,
        private ResponseBuilderInterface $responseBuilder,
        private InteractionsStorageInterface $storage,
        private ProviderVerifierInterface $providerVerifier,
    ) {
    }

    /**
     * @Given a provider is started that returns the response from interaction :id
     */
    public function aProviderIsStartedThatReturnsTheResponseFromInteraction(int $id): void
    {
        $this->server->register($id);
    }

    /**
     * @Given a Pact file for interaction :id is to be verified
     */
    public function aPactFileForInteractionIsToBeVerified(int $id): void
    {
        $pactPath = new PactPath("c-$id");
        $this->pactWriter->write($id, $pactPath);
        $this->providerVerifier->addSource($pactPath);
    }

    /**
     * @Given a provider is started that returns the responses from interactions :ids
     */
    public function aProviderIsStartedThatReturnsTheResponsesFromInteractions(string $ids): void
    {
        $ids = array_map(fn (string $id) => (int) trim($id), explode(',', $ids));
        $this->server->register(...$ids);
    }

    /**
     * @Given a Pact file for interaction :id is to be verified from a Pact broker
     */
    public function aPactFileForInteractionIsToBeVerifiedFromAPactBroker(int $id): void
    {
        $pactPath = new PactPath("c-$id");
        $this->pactWriter->write($id, $pactPath);
        $this->pactBroker->publish($id);
        $broker = new Broker();
        $broker->setUrl(new Uri('http:/localhost:9292'));
        $this->providerVerifier->addSource($broker);
    }

    /**
     * @Then a verification result will NOT be published back
     */
    public function aVerificationResultWillNotBePublishedBack(): void
    {
        Assert::assertSame(1, $this->pactBroker->getMatrix()['summary']['unknown']);
    }

    /**
     * @Given publishing of verification results is enabled
     */
    public function publishingOfVerificationResultsIsEnabled(): void
    {
        $publishOptions = new PublishOptions();
        $publishOptions
            ->setProviderVersion('1.2.3')
        ;
        $this->providerVerifier->getConfig()->setPublishOptions($publishOptions);
    }

    /**
     * @Then a successful verification result will be published back for interaction {:id}
     */
    public function aSuccessfulVerificationResultWillBePublishedBackForInteraction(int $id): void
    {
        Assert::assertSame(1, $this->pactBroker->getMatrix()['summary']['success']);
    }

    /**
     * @Then a failed verification result will be published back for the interaction {:id}
     */
    public function aFailedVerificationResultWillBePublishedBackForTheInteraction(int $id): void
    {
        Assert::assertSame(1, $this->pactBroker->getMatrix()['summary']['failed']);
    }

    /**
     * @Given a Pact file for interaction :id is to be verified with a provider state :state defined
     */
    public function aPactFileForInteractionIsToBeVerifiedWithAProviderStateDefined(int $id, string $state): void
    {
        $pactPath = new PactPath("c-$id");
        $this->pactWriter->write($id, $pactPath);
        $pact = json_decode(file_get_contents($pactPath), true);
        $pact['interactions'][0]['providerStates'][] = ['name' => $state];
        file_put_contents($pactPath, json_encode($pact));
        $this->providerVerifier->addSource($pactPath);
    }

    /**
     * @Then a warning will be displayed that there was no provider state callback configured for provider state :state
     */
    public function aWarningWillBeDisplayedThatThereWasNoProviderStateCallbackConfiguredForProviderState(string $state): never
    {
        throw new PendingException("Unable to verify this, as I can't find a way to assert this message from verifier's log: 'pact_verifier::callback_executors: State Change ignored as there is no state change URL provided for interaction'");
    }

    /**
     * @Given a request filter is configured to make the following changes:
     */
    public function aRequestFilterIsConfiguredToMakeTheFollowingChanges(TableNode $table): never
    {
        throw new PendingException("Unable to set request filter callback from ffi");
    }

    /**
     * @Then the request to the provider will contain the header :header
     */
    public function theRequestToTheProviderWillContainTheHeader(string $header): never
    {
        throw new PendingException('Unable to set request filter callback from ffi, so no need to implement this step');
    }

    /**
     * @Given a provider is started that returns the response from interaction :id, with the following changes:
     */
    public function aProviderIsStartedThatReturnsTheResponseFromInteractionWithTheFollowingChanges(int $id, TableNode $table): void
    {
        $response = $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $id)->getResponse();
        $this->responseBuilder->build($response, $table->getHash()[0]);
        $this->server->register($id);
    }
}
