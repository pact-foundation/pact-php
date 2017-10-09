<?php

namespace PhpPact\Mocks\MockHttpService\Models;

class ProviderServiceResponse implements \JsonSerializable, \PhpPact\Mocks\MockHttpService\Models\IHttpMessage
{

    private $_bodyWasSet;
    private $_body;
    private $_headers = []; //[JsonProperty(PropertyName = "headers")] / [JsonConverter(typeof(PreserveCasingDictionaryConverter))]
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
    public function getStatus()
    {
        return $this->_status;
    }


    public function __construct($status = null, array $headers = array(), $body = null)
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

        if (is_string($body) && strtolower($body) === "null") {
            $body = null;
        }

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

        if ($this->getContentType() == "application/json") {
            $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatcher(true);
        } else if ($this->getContentType() == "text/plain") {
            $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatcher();
        }
        else {
            // make JSON the default based on specification tests
            $this->_matchingRules[] = new \PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatcher(true);
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
        $obj['status'] = intval($this->_status);
        $obj['headers'] = $this->_headers;

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

        \json_decode($obj, true);
        if (\json_last_error()) {
            return false;
        }

        return true;
    }

}
