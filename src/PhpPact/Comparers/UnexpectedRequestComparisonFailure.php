<?php

namespace PhpPact\Comparers;

use PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction;

class UnexpectedRequestComparisonFailure extends ComparisonFailure
{
    public $_requestDescription;

    public function __construct($request)
    {
        /**
         * @var ProviderServiceInteraction
         */
        $requestMethod = $request != null ? \strtoupper($request->getMethod()) : 'No Method';
        $requestPath   = $request != null ? $request->getPath() : 'No Path';

        $this->_requestDescription = \sprintf('%s %s', $requestMethod, $requestPath);
        $this->_result             = \sprintf(
            'An unexpected request %s was seen by the mock provider service.',
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
