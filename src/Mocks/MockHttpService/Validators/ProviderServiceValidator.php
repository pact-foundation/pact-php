<?php

namespace PhpPact\Mocks\MockHttpService\Validators;

use PHPUnit\Runner\Exception;

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

    public function __construct(\PhpPact\Mocks\MockHttpService\IHttpRequestSender $httpRequestSender, \PhpPact\Reporters\Reporter $reporter, \PhpPact\PactVerifierConfig $config)
    {
        $this->_providerServiceResponseComparer = new \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer();
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
    public function Validate(\PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile $pactFile, \PhpPact\Models\ProviderStates $providerStates)
    {
        if (!$pactFile->getConsumer()) {
            throw new \InvalidArgumentException("Please supply a non null or empty Consumer name in the pactFile");
        }

        if (!$pactFile->getProvider()) {
            throw new \InvalidArgumentException("Please supply a non null or empty Provider name in the pactFile");
        }

        if ($pactFile->getInteractions() != null && count($pactFile->getInteractions()) > 0) {
            $this->_reporter->ReportInfo(sprintf("Verifying a Pact between %s and %s", $pactFile->getConsumer()->getName(), $pactFile->getProvider()->getName()));

            $comparisonResult = new \PhpPact\Comparers\ComparisonResult();

            foreach ($pactFile->getInteractions() as $interaction) {

                /**
                 * @var $interaction \PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction;
                 */

                $this->InvokePactSetUpIfApplicable($providerStates);

                $this->_reporter->ResetIndentation();

                $providerStateItem = null; // better name?

                if ($interaction->getProviderState() != null) {
                    try {
                        $providerStateItem = $providerStates->Find($interaction->getProviderState());
                    } catch (Exception $e) {
                        throw new \Exception(sprintf("providerState '%s' was defined by a consumer, however could not be found. Please supply this provider state.", $interaction->getProviderState()), $e);
                    }
                }

                $this->InvokeProviderStateSetUpIfApplicable($providerStateItem);

                if (!$interaction->getProviderState()) {
                    $this->_reporter->Indent();
                    $this->_reporter->ReportInfo(sprintf("Given %s", $interaction->getProviderState()));
                }

                $this->_reporter->Indent();
                $this->_reporter->ReportInfo(sprintf("%s", $interaction->getDescription()));

                if (!$interaction->getRequest()) {
                    $this->_reporter->Indent();
                    $this->_reporter->ReportInfo(sprintf("with %s %s", $interaction->getRequest()->getMethod(), $interaction->getRequest()->getPath()));
                }

                try {
                    $interactionComparisonResult = $this->ValidateInteraction($interaction);
                    $comparisonResult->AddChildResult($interactionComparisonResult);

                    $this->_reporter->Indent();
                    $this->_reporter->ReportSummary($interactionComparisonResult);
                } finally {
                    $this->InvokeProviderStateTearDownIfApplicable($providerStateItem);
                    $this->InvokeTearDownIfApplicable($providerStates);
                }
            }

            $this->_reporter->ResetIndentation();
            $this->_reporter->ReportFailureReasons($comparisonResult);
            $this->_reporter->Flush();

            //$this->_config->getLogger()->debug('**** Logging all results: ' . print_r($comparisonResult, true));

            if ($comparisonResult->HasFailure()) {
                throw new \PhpPact\PactFailureException("See test output or logs for failure details.");
            }
        }
    }


    /**
     * @param ProviderServiceInteraction $
     * @return \PhpPact\Comparers\ComparisonResult ComparisonResult
     */
    private function ValidateInteraction(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction $interaction)
    {
        $expectedResponse = $interaction->getResponse();
        $actualResponse = $this->_httpRequestSender->Send($interaction->getRequest(), $this->_config->getBaseUri());
        $results = $this->_providerServiceResponseComparer->Compare($expectedResponse, $actualResponse);

        return $results;
    }

    private function InvokePactSetUpIfApplicable(\PhpPact\Models\ProviderStates $providerStates)
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

    private function InvokeTearDownIfApplicable(\PhpPact\Models\ProviderStates $providerStates)
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
    private function InvokeProviderStateSetUpIfApplicable($providerState)
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
    private function InvokeProviderStateTearDownIfApplicable($providerState)
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
