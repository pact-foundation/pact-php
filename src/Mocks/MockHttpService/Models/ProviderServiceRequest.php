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

    public function __construct($method, $path, $headers, $body = null)
    {
        // enumerate over HttpVerb to set the value of the
        $verb = new \PhpPact\Mocks\MockHttpService\Models\HttpVerb();
        $this->_method = $verb->Enum($method);
        $this->_path = $path;
        $this->_headers = $headers;

        if ($body) {
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
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return mixed
     */
    public function setHeaders($headers)
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

    public function PathWithQuery()
    {
        if (!$this->_path && !$this->Query) {
            throw new \RuntimeException("Query has been supplied, however Path has not. Please specify as Path.");
        }

        return !($this->Query) ?
            sprintf("%s?%s", $this->_path, $this->Query) :
            $this->_path;
    }

    private function ParseBodyMatchingRules($body)
    {
        $this->_matchingRules = array();
        $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\DefaultHttpBodyMatcher(false);

        return $body;
    }

    function jsonSerialize()
    {
        // this _should_ cascade to child classes
        $obj = new \stdClass();
        $obj->method = $this->_method;
        $obj->path = $this->_path;

        if ($this->_query) {
            $obj->query = $this->_query;
        }

        $header = $this->_headers;
        if (is_array($header)) {
            $header = (object)$header;
        }
        $obj->headers = $header;

        if ($this->_body) {
            $obj->body = $this->_body;

            if ($this->isJsonString($obj->body)) {
                $obj->body = \json_decode($obj->body);;
            }
        }

        return $obj;
    }

    private function isJsonString($obj)
    {
        if ($obj === '') {
            return false;
        }

        \json_decode($obj);
        if (\json_last_error()) {
            return false;
        }

        return true;
    }

}