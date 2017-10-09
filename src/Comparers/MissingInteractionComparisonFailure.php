<?php

namespace PhpPact\Comparers;

class MissingInteractionComparisonFailure extends ComparisonFailure
{
    public $_requestDescription;

    public function __construct($interaction)
    {
        /**
         * @var $interaction \PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction
         */
        $requestMethod = $interaction->getRequest() != null ? strtoupper($interaction->getRequest()->getMethod()) : "No Method";
        $requestPath = $interaction->getRequest() != null ? $interaction->getRequest()->getPath() : "No Path";

        $this->_requestDescription = sprintf("%s %s", $requestMethod, $requestPath);
        $this->_result = sprintf(
            "The interaction with description '%s' and provider state '%s', was not used by the test. Missing request %s.",
            $interaction->getDescription(),
            $interaction->getProviderState(),
            $this->_requestDescription
        );
    }

    /**
     * @return string
     */
    public function getRequestDescription()
    {
        return $this->_requestDescription;
    }
}
