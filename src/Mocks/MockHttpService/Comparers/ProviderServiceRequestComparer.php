<?php
namespace PhpPact\Mocks\MockHttpService\Comparers;

class ProviderServiceRequestComparer
{
    private $_httpMethodComparer; //IHttpStatusCodeComparer
    private $_httpPathComparer; //IHttpHeaderComparer
    private $_httpQueryStringComparer; //IHttpQueryStringComparer
    private $_httpHeaderComparer; //IHttpHeaderComparer
    private $_httpBodyComparer; //IHttpBodyComparer

    public function __construct()
    {
        $this->_httpMethodComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpMethodComparer();
        $this->_httpPathComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpPathComparer();
        $this->_httpQueryStringComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpQueryStringComparer();
        $this->_httpHeaderComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpHeaderComparer();
        $this->_httpBodyComparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpBodyComparer();
    }

    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual)
    {
        $result = new \PhpPact\Comparers\ComparisonResult("returns a response which");

        if (!$expected) {
            $result->RecordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure(__CLASS__ . ": Expected is null"));
            return $result;
        }

        $methodResult = $this->_httpMethodComparer->Compare($expected->getMethod(), $actual->getMethod());
        $result->AddChildResult($methodResult);

        $pathResult = $this->_httpPathComparer->Compare($expected->getPath(), $actual->getPath());
        $result->AddChildResult($pathResult);

        $queryResult = $this->_httpQueryStringComparer->Compare($expected->getQuery(), $actual->getQuery());
        $result->AddChildResult($queryResult);

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
