<?php
namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers;

class ProviderServiceRequestComparer
{
    private $_httpMethodComparer; //IHttpStatusCodeComparer
    private $_httpPathComparer; //IHttpHeaderComparer
    private $_httpQueryStringComparer; //IHttpQueryStringComparer
    private $_httpHeaderComparer; //IHttpHeaderComparer
    private $_httpBodyComparer; //IHttpBodyComparer

    public function __construct()
    {
        $this->_httpMethodComparer = new HttpMethodComparer();
        $this->_httpPathComparer = new HttpPathComparer();
        $this->_httpQueryStringComparer = new HttpQueryStringComparer();
        $this->_httpHeaderComparer = new HttpHeaderComparer();
        $this->_httpBodyComparer = new HttpBodyComparer();
    }

    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $result = new Comparers\ComparisonResult("returns a response which");

        if (!$expected) {
            $result->recordFailure(new Comparers\ErrorMessageComparisonFailure(__CLASS__ . ": Expected is null"));
            return $result;
        }

        $methodResult = $this->_httpMethodComparer->compare($expected->getMethod(), $actual->getMethod());
        $result->addChildResult($methodResult);

        $pathResult = $this->_httpPathComparer->compare($expected->getPath(), $actual->getPath());
        $result->addChildResult($pathResult);

        $queryResult = $this->_httpQueryStringComparer->compare($expected->getQuery(), $actual->getQuery());
        $result->addChildResult($queryResult);

        if (count($expected->getHeaders()) > 0) {
            $headerResult = $this->_httpHeaderComparer->compare($expected->getHeaders(), $actual->getHeaders());
            $result->addChildResult($headerResult);
        }

        // handles case where body is set but null
        // If there has already been a faillure, do not check the body
        // Failed header settings can result in the body processing to fail
        if ($expected->shouldSerializeBody() && !$result->HasFailure()) {
            $bodyResult = $this->_httpBodyComparer->compare($expected, $actual, $expected->getBodyMatchers(), $expected->getContentType());
            $result->addChildResult($bodyResult);
        }

        return $result;
    }
}
