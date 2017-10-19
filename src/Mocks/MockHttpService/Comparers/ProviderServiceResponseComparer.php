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
    public function Compare($expected, $actual)
    {
        $result = new ComparisonResult("returns a response which");
        if (!$expected) {
            $result->RecordFailure(new ErrorMessageComparisonFailure(__CLASS__ . ": Expected is null"));
            return $result;
        }

        $statusResult = $this->_httpStatusCodeComparer->Compare($expected->getStatus(), $actual->getStatus());
        $result->AddChildResult($statusResult);

        if (count($expected->getHeaders()) > 0) {
            $headerResult = $this->_httpHeaderComparer->Compare($expected->getHeaders(), $actual->getHeaders());
            $result->AddChildResult($headerResult);
        }

        // handles case where body is set but null
        // If there has already been a faillure, do not check the body
        // Failed header settings can result in the body processing to fail
        if ($expected->shouldSerializeBody() && !$result->HasFailure()) {
            $bodyResult = $this->_httpBodyComparer->Compare($expected, $actual, $expected->getBodyMatchers(), $expected->getContentType());
            $result->AddChildResult($bodyResult);
        }

        return $result;
    }
}
