<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

class ProviderServiceResponseComparer
{
    private $_httpStatusCodeComparer; //IHttpStatusCodeComparer
    private $_httpHeaderComparer; //IHttpHeaderComparer
    private $_httpBodyComparer; //IHttpBodyComparer

    public function __construct()
    {
        $this->_httpStatusCodeComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpStatusCodeComparer();
        $this->_httpHeaderComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpHeaderComparer();
        $this->_httpBodyComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpBodyComparer();
    }


    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual)
    {
        $result = new \PhpPact\Comparers\ComparisonResult("returns a response which");
        if (!$expected) {
            $result->RecordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure(__CLASS__ . ": Expected is null"));
            return $result;
        }

        $statusResult = $this->_httpStatusCodeComparer->Compare($expected->getStatus(), $actual->getStatus());
        $result->AddChildResult($statusResult);

        if (count($expected->getHeaders()) > 0) {
            $headerResult = $this->_httpHeaderComparer->Compare($expected->getHeaders(), $actual->getHeaders());
            $result->AddChildResult($headerResult);
        }

        // handles case where body is set but null
        if ($expected->ShouldSerializeBody()) {
            $bodyResult = $this->_httpBodyComparer->Compare($expected, $actual, $expected->getMatchingRules());
            $result->AddChildResult($bodyResult);
        }

        return $result;
    }
}
