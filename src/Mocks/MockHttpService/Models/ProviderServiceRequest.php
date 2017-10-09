<?php

namespace PhpPact\Mocks\MockHttpService\Models;

class ProviderServiceRequest implements \JsonSerializable, \PhpPact\Mocks\MockHttpService\Models\IHttpMessage
{

    private $_bodyWasSet;
    private $_body;
    private $_method; // use setMethod
    private $_path; //[JsonProperty(PropertyName = "path")]
    private $_headers; //[JsonProperty(PropertyName = "headers")] / [JsonConverter(typeof(PreserveCasingDictionaryConverter))]

    private $_matchingRules;
    private $_query; //[JsonProperty(PropertyName = "query")]

    public function __construct($method, $path, array $headers = [], $body = false)
    {
        // enumerate over HttpVerb to set the value of the
        $verb = new \PhpPact\Mocks\MockHttpService\Models\HttpVerb();
        $this->_method = $verb->Enum($method);
        $this->_path = $path;
        $this->_headers = $headers;

        if ($body !== false) {
            $this->setBody($body);
        }
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        if ($this->_bodyWasSet)
        {
            return $this->_body;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function setBody($body)
    {
        $this->_bodyWasSet = true;

        if (is_string($body) && strtolower($body) === "null") {
            $body = null;
        }

        $this->_body = $this->ParseBodyMatchingRules($body);

        return false;
    }

    /**
     * @return int|mixed
     */
    public function getMethod()
    {
        return $this->_method;
    }


    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->_path;
    }


    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->_headers;
    }

    /**
     * @param array headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        if (isset($this->_query)) {
            return $this->_query;
        }

        return false;
    }

    /**
     * @param mixed $Query
     */
    public function setQuery($query)
    {
        $this->_query = $query;
    }

    /**
     * @return mixed
     */
    public function getMatchingRules()
    {
        return $this->_matchingRules;
    }


    public function ShouldSerializeBody()
    {
        return $this->_bodyWasSet;
    }

    // @todo this function is not used and is faulty! Remove?
    public function PathWithQuery()
    {
        if (!$this->_path && !$this->Query) {
            throw new \RuntimeException("Query has been supplied, however Path has not. Please specify as Path.");
        }

        return !$this->Query ?
            sprintf("%s?%s", $this->_path, $this->Query) :
            $this->_path;
    }

    private function ParseBodyMatchingRules($body)
    {

        $this->_matchingRules = array();

        if ($this->getContentType() == "application/json") {
            $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatcher(false);
        } else if ($this->getContentType() == "text/plain") {
            $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatcher();
        }
        else {
            // make JSON the default based on specification tests
            $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatcher(false);
        }

        return $body;
    }

    /**
     * Return the header value for Content-Type
     *
     * False is returned if not set
     *
     * @return mixed|bool
     */
    public function getContentType()
    {
        $headers = $this->getHeaders();

        return $headers['Content-Type'] ?? false;
    }


    public function jsonSerialize()
    {
        // this _should_ cascade to child classes
        $obj = [];
        $obj['method'] = $this->_method;
        $obj['path'] = $this->_path;

        if ($this->_query) {
            $obj['query'] = $this->_query;
        }

        if ($this->_headers){
            $obj['headers'] = $this->_headers;
        }

        if ($this->_body) {
            $obj['body'] = $this->_body;

            if ($this->isJsonString($obj['body'])) {
                $obj['body'] = \json_decode($obj['body'], true);
            }
        }

        return $obj;
    }

    private function isJsonString($obj)
    {
        if ($obj === '') {
            return false;
        }

        @\json_decode($obj, true);
        if (\json_last_error()) {
            return false;
        }

        return true;
    }

}
