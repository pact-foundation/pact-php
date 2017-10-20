<?php

namespace PhpPact\Mocks\MockHttpService\Validators;

use PhpPact\PactVerifierConfig;
use PhpPact\Mocks\MockHttpService;
use PhpPact\Reporters\Reporter;
use PhpPact\Models\ProviderStates;
use PhpPact\Comparers\ComparisonResult;

class ProviderServiceValidator
{

    /**
     * @var \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer
     */
    private $_providerServiceResponseComparer;

    /**
     * @var \PhpPact\Mocks\MockHttpService\HttpClientRequestSender
     */
    private $_httpRequestSender;

    /**
     * @var \PhpPact\Reporters\Reporter
     */
    private $_reporter;

    /**
     * @var \PhpPact\PactVerifierConfig
     */
    private $_config;

    public function __construct(MockHttpService\IHttpRequestSender $httpRequestSender, Reporter $reporter, PactVerifierConfig $config)
    {
        $this->_providerServiceResponseComparer = new MockHttpService\Comparers\ProviderServiceResponseComparer();
        $this->_httpRequestSender = $httpRequestSender;
        $this->_reporter = $reporter;
        $this->_config = $config;
    }

    /**
     * Validate the pact file and send a request to a mock server
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile $pactFile
     * @param \PhpPact\Models\ProviderStates $providerStates
     * @throws \Exception
     * @throws \PhpPact\PactFailureException
     */
    public function validate(MockHttpService\Models\ProviderServicePactFile $pactFile, ProviderStates $providerStates)
    {
        if (!$pactFile->getConsumer()) {
            throw new \InvalidArgumentException("Please supply a non null or empty Consumer name in the pactFile");
        }

        if (!$pactFile->getProvider()) {
            throw new \InvalidArgumentException("Please supply a non null or empty Provider name in the pactFile");
        }

        if ($pactFile->getInteractions() != null && count($pactFile->getInteractions()) > 0) {
            $this->_reporter->reportInfo(sprintf("Verifying a Pact between %s and %s", $pactFile->getConsumer()->getName(), $pactFile->getProvider()->getName()));

            $comparisonResult = new ComparisonResult();

            foreach ($pactFile->getInteractions() as $interaction) {

                /**
                 * @var $interaction \PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction;
                 */

                $this->invokePactSetUpIfApplicable($providerStates);

                $this->_reporter->resetIndentation();

                $providerStateItem = null; // better name?

                if ($interaction->getProviderState() != null) {
                    try {
                        $providerStateItem = $providerStates->find($interaction->getProviderState());
                    } catch (\Exception $e) {
                        throw new \Exception(sprintf("providerState '%s' was defined by a consumer, however could not be found. Please supply this provider state.", $interaction->getProviderState()), $e);
                    }
                }

                $this->invokeProviderStateSetUpIfApplicable($providerStateItem);

                if (!$interaction->getProviderState()) {
                    $this->_reporter->indent();
                    $this->_reporter->reportInfo(sprintf("Given %s", $interaction->getProviderState()));
                }

                $this->_reporter->indent();
                $this->_reporter->reportInfo(sprintf("%s", $interaction->getDescription()));

                if (!$interaction->getRequest()) {
                    $this->_reporter->indent();
                    $this->_reporter->reportInfo(sprintf("with %s %s", $interaction->getRequest()->getMethod(), $interaction->getRequest()->getPath()));
                }

                try {
                    $interactionComparisonResult = $this->validateInteraction($interaction);
                    $comparisonResult->addChildResult($interactionComparisonResult);

                    $this->_reporter->indent();
                    $this->_reporter->reportSummary($interactionComparisonResult);
                } finally {
                    $this->invokeProviderStateTearDownIfApplicable($providerStateItem);
                    $this->invokeTearDownIfApplicable($providerStates);
                }
            }

            $this->_reporter->resetIndentation();
            $this->_reporter->reportFailureReasons($comparisonResult);
            $this->_reporter->flush();

            if ($comparisonResult->hasFailure()) {
                throw new \PhpPact\PactFailureException("See test output or logs for failure details.");
            }
        }
    }


    /**
     * @param ProviderServiceInteraction $
     * @return \PhpPact\Comparers\ComparisonResult ComparisonResult
     */
    private function validateInteraction(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction $interaction)
    {
        $expectedResponse = $interaction->getResponse();
        $actualResponse = $this->_httpRequestSender->Send($interaction->getRequest(), $this->_config->getBaseUri());
        $results = $this->_providerServiceResponseComparer->compare($expectedResponse, $actualResponse);

        return $results;
    }

    private function invokePactSetUpIfApplicable(\PhpPact\Models\ProviderStates $providerStates)
    {
        if ($providerStates->count() > 0 && $providerStates->SetUp != null) {
            $func = $providerStates->SetUp;

            /**
             * @var \Closure $func
             */
            $func();

            return true;
        }

        return false;
    }

    private function invokeTearDownIfApplicable(ProviderStates $providerStates)
    {
        if ($providerStates->count() > 0 && $providerStates->TearDown != null) {
            $func = $providerStates->TearDown;

            /**
             * @var \Closure $func
             */
            $func();

            return true;
        }

        return false;
    }

    /**
     * @param $providerState \PhpPact\Models\ProviderState|null
     * @return bool
     */
    private function invokeProviderStateSetUpIfApplicable($providerState)
    {
        /*
         * @var $providerState \PhpPact\Models\ProviderState
         */
        if ($providerState != null && $providerState->SetUp != null) {
            $func = $providerState->SetUp;

            /**
             * @var \Closure $func
             */
            $func();

            return true;
        }

        return false;
    }

    /**
     * @param $providerState \PhpPact\Models\ProviderState|null
     * @return bool
     */
    private function invokeProviderStateTearDownIfApplicable($providerState)
    {
        /*
         * @var $providerState \PhpPact\Models\ProviderState
         */

        if ($providerState != null && $providerState->TearDown != null) {
            $func = $providerState->TearDown;

            /**
             * @var \Closure $func
             */
            $func();

            return true;
        }

        return false;
    }
}
