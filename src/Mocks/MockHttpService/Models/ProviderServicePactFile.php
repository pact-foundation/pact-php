<?php

namespace PhpPact\Mocks\MockHttpService\Models;

use PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer;
use PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceRequestComparer;

class ProviderServicePactFile extends \PhpPact\Models\PactFile implements \JsonSerializable
{
    private $_interactions;

    public function __construct()
    {
        if (is_callable('parent::__construct')) {
            parent::__construct();
        }

        $this->_interactions = array();
    }

    public function jsonSerialize()
    {
        $obj = parent::jsonSerialize();
        $obj->interactions = $this->_interactions;

        return $obj;
    }

    public function setInteractions($interactionArray)
    {
        // initialize Interactions
        $this->_interactions = array();

        if (!is_array($interactionArray)) {
            throw new \InvalidArgumentException('$interactionArray is not an array: ' . get_class($interactionArray));
        }

        if (count($interactionArray) == 0) {
            $this->_interactions = array();
            return $this->_interactions;
        }

        foreach ($interactionArray as $interactionObj) {
            if (!isset($interactionObj->description)) {
                throw new \RuntimeException("description is not set");
            }

            if (!isset($interactionObj->providerState) && !isset($interactionObj->provider_state)) {
                throw new \RuntimeException("provider state is not set");
            }


            $this->addInteraction($interactionObj);
        }

        return $this->_interactions;
    }

    public function addInteraction($interactionObj)
    {
        $interaction = $interactionObj;

        if (!($interactionObj instanceof ProviderServiceInteraction)) {
            $interaction = new ProviderServiceInteraction();

            if (isset($interactionObj->providerState)) {
                $interaction->setProviderState($interactionObj->providerState);
            } else if (isset($interactionObj->provider_state)) {
                $interaction->setProviderState($interactionObj->provider_state);
            }

            $interaction->setDescription($interactionObj->description);
            $interaction->setRequest($interactionObj->request);
            $interaction->setResponse($interactionObj->response);
        }

        $this->_interactions[] = $interaction;
    }

    public function getInteractions()
    {
        if (!$this->_interactions) {
            return array();
        }

        return $this->_interactions;
    }


    /**
     * All interactions other than those matching this description will be removed
     *
     * @param $description
     * @return array
     */
    public function filterInteractionsByDescription($description)
    {
        $filteredInteractions = array();

        if (count($this->_interactions) > 0) {
            foreach ($this->_interactions as $interaction) {
                if (strtolower($description) == strtolower($interaction->getDescription())) {
                    $filteredInteractions[] = $interaction;
                }
            }
        }

        $this->_interactions = $filteredInteractions;
        return $filteredInteractions;
    }

    /**
     * All interactions other than those matching this provider state will be removed
     *
     * @param $state
     * @return array
     */
    public function filterInteractionsByProviderState($state)
    {
        $filteredInteractions = array();

        if (count($this->_interactions) > 0) {
            foreach ($this->_interactions as $interaction) {
                if (strtolower($state) == strtolower($interaction->getProviderState())) {
                    $filteredInteractions[] = $interaction;
                }
            }
        }

        $this->_interactions = $filteredInteractions;
        return $filteredInteractions;
    }

    /**
     * Cycle through interaction to see if we compare to the passed in response.  Similar to filter functions except the object is not modified
     *
     * @param ProviderServiceResponse $response
     * @return mixed|ProviderServiceInteraction
     * @throws \PhpPact\PactFailureException(
     */
    public function findInteractionsByProviderServiceResponse(ProviderServiceResponse $response)
    {
        $responseComparer = new ProviderServiceResponseComparer();

        foreach ($this->_interactions as $interaction) {
            /**
             * @var $interaction ProviderServiceInteraction
             */
            $interactionResponse = $interaction->getResponse();
            $comparisionResults = $responseComparer->compare($interactionResponse, $response);

            if (!($comparisionResults->hasFailure())) {
                return $interaction;
            }
        }

        throw new \PhpPact\PactFailureException("No interaction found matching this respose");
    }

    /**
     * Cycle through interaction to see if we compare to the passed in request.  Similar to filter functions except the object is not modified
     *
     * @param ProviderServiceRequest $request
     * @return mixed|ProviderServiceInteraction
     * @throws \PhpPact\PactFailureException
     */
    public function findInteractionByProviderServiceRequest(ProviderServiceRequest $request)
    {
        $requestComparer = new ProviderServiceRequestComparer();

        foreach ($this->_interactions as $interaction) {
            /**
             * @var $interaction ProviderServiceInteraction
             */
            $interactionRequest = $interaction->getRequest();
            $comparisionResults = $requestComparer->compare($interactionRequest, $request);

            if (!($comparisionResults->hasFailure())) {
                return $interaction;
            }
        }

        if (isset($this->_logger)) {
            $msg = "Unable to find";
            $msg .= "\nRequest:". print_r($request, true);
            $msg .= "\nInteractions: " . print_r($this->_interactions, true);
            $this->_logger->debug($msg);
        }

        throw new \PhpPact\PactFailureException("No interaction found matching this request");
    }
}
