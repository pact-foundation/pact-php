<?php

namespace PhpPact\Mocks\MockHttpService\Models;

class HandledRequest
{

    public $_actualRequest;
    public $_matchedInteraction;

    public function __construct($actualRequest, $matchedInteraction)
    {
        $this->_actualRequest = $actualRequest;
        $this->_matchedInteraction = $matchedInteraction;
    }

    /**
     * @return mixed
     */
    public function getActualRequest()
    {
        return $this->_actualRequest;
    }

    /**
     * @return mixed
     */
    public function getMatchedInteraction()
    {
        return $this->_matchedInteraction;
    }

}