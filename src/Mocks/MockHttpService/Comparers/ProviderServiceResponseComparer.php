<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers\ComparisonResult;
use PhpPact\Comparers\ErrorMessageComparisonFailure;

class ProviderServiceResponseComparer
{
    private $_httpStatusCodeComparer; //IHttpStatusCodeComparer
    private $_httpHeaderComparer; //IHttpHeaderComparer
    private $_httpBodyComparer; //IHttpBodyComparer

    public function __construct()
    {
        $this->_httpStatusCodeComparer = new HttpStatusCodeComparer();
        $this->_httpHeaderComparer = new HttpHeaderComparer();
        $this->_httpBodyComparer = new HttpBodyComparer();
    }


    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     *
     * @return ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $result = new ComparisonResult("returns a response which");
        if (!$expected) {
            $result->recordFailure(new ErrorMessageComparisonFailure(__CLASS__ . ": Expected is null"));
            return $result;
        }

        $statusResult = $this->_httpStatusCodeComparer->compare($expected->getStatus(), $actual->getStatus());
        $result->addChildResult($statusResult);

        if (count($expected->getHeaders()) > 0) {
            $headerResult = $this->_httpHeaderComparer->compare($expected->getHeaders(), $actual->getHeaders(), $expected->getMatchingRules());
            $result->addChildResult($headerResult);
        }

        // handles case where body is set but null
        // If there has already been a faillure, do not check the body
        // Failed header settings can result in the body processing to fail
        if ($expected->shouldSerializeBody() && !$result->hasFailure()) {
            $bodyResult = $this->_httpBodyComparer->compare($expected, $actual);
            $result->addChildResult($bodyResult);
        }

        return $result;
    }
}
