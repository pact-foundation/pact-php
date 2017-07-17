<?php

namespace PhpPact\Mocks\MockHttpService\Models;

class ProviderServiceResponse implements \JsonSerializable, \PhpPact\Mocks\MockHttpService\Models\IHttpMessage
{

    private $_bodyWasSet;
    private $_body;
    private $_headers; //[JsonProperty(PropertyName = "headers")] / [JsonConverter(typeof(PreserveCasingDictionaryConverter))]
    private $_status;
    private $_matchingRules;

    /**
     * @return mixed
     */
    public function getBody()
    {
        if (!isset($this->_body)) {
            return false;
        }
        return $this->_body;
    }

    /**
     * @return mixed
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
    public function getStatus()
    {
        return $this->_status;
    }


    public function __construct($status, $headers, $body = null)
    {
        $this->_status = $status;
        $this->_headers = $headers;

        if ($body) {
            $this->setBody($body);
        }
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

    public function ShouldSerializeBody()
    {
        return $this->_bodyWasSet;
    }


    /**
     * @return mixed
     */
    public function getMatchingRules()
    {
        return $this->_matchingRules;
    }

    /**
     * @param mixed $matchingRules
     */
    public function setMatchingRules($matchingRules)
    {
        $this->_matchingRules = $matchingRules;
    }


    private function ParseBodyMatchingRules($body)
    {
        $this->_matchingRules = array();
        //$this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatcher();
        $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\DefaultHttpBodyMatcher(true);

        return $body;
    }


    function jsonSerialize()
    {
        // this _should_ cascade to child classes
        $obj = new \stdClass();
        $obj->status = intval($this->_status);

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