<?php

namespace PhpPact\Mocks\MockHttpService\Models;

use PhpPact\Matchers\Rules\MatchingRule;

class ProviderServiceResponse implements \JsonSerializable, \PhpPact\Mocks\MockHttpService\Models\IHttpMessage
{
    private $_bodyWasSet;
    private $_body;
    private $_headers; //[JsonProperty(PropertyName = "headers")] / [JsonConverter(typeof(PreserveCasingDictionaryConverter))]
    private $_status;
    private $_bodyMatchers;
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


    public function __construct($status = null, $headers = array(), $body = null, $matchingRules = array())
    {
        $this->_status = $status;
        $this->_headers = $headers;

        if ($body) {
            $this->setBody($body);
        }

        $this->setMatchingRules($matchingRules);
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

        $this->_body = $this->parseBodyMatchingRules($body);

        return false;
    }

    public function shouldSerializeBody()
    {
        return $this->_bodyWasSet;
    }

    /**
     * @return array
     */
    public function getMatchingRules()
    {
        return $this->_matchingRules;
    }

    /**
     * @param array|false $matchingRules
     */
    public function setMatchingRules($matchingRules)
    {
        if (count($matchingRules) > 0) {
            foreach ($matchingRules as $matchingRule) {
                $this->addMatchingRule($matchingRule);
            }
        }
    }

    /**
     * Add a single matching rule
     *
     * @param MatchingRule $matchingRule
     */
    public function addMatchingRule(MatchingRule $matchingRule)
    {
        $this->_matchingRules[$matchingRule->getJsonPath()] = $matchingRule;
    }


    /**
     * @return mixed
     */
    public function getBodyMatchers()
    {
        return $this->_bodyMatchers;
    }

    /**
     * @param mixed $matchingRules
     */
    public function setBodyMatchers($matchingRules)
    {
        $this->_bodyMatchers = $matchingRules;
    }


    private function parseBodyMatchingRules($body)
    {
        $this->_bodyMatchers = array();

        if ($this->getContentType() == "application/json") {
            $this->_bodyMatchers[] = new \PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatchChecker(true);
        } elseif ($this->getContentType() == "text/plain") {
            $this->_bodyMatchers[] = new \PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatchChecker();
        } elseif ($this->getContentType() == "application/xml") {
            $this->_bodyMatchers[] = new \PhpPact\Mocks\MockHttpService\Matchers\XmlHttpBodyMatchChecker(true);
        } else {
            // make JSON the default based on specification tests
            $this->_bodyMatchers[] = new \PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatchChecker(true);
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
        $key = 'Content-Type';

        if (is_array($headers) && isset($headers[$key])) {
            return $headers[$key];
        }

        return 'application/json';
    }


    public function jsonSerialize()
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
            if ($this->isJsonString($this->_body)) {
                $obj->body = \json_decode($this->_body);
            } else {
                $obj->body = $this->_body;
            }
        }

        if (count($this->_matchingRules) > 0) {
            $obj->matchingRules = new \stdClass();
            foreach($this->_matchingRules as $matchingRuleVo) {

                /**
                 * @var $matchingRuleVo MatchingRule
                 */
                $jsonPath = $matchingRuleVo->getJsonPath();
                $obj->matchingRules->$jsonPath = $matchingRuleVo->jsonSerialize();
            }
        }

        return $obj;
    }

    private function isJsonString($string) {
        if (!is_string($string)) {
            return false;
        }
        \json_decode($string);
        return (\json_last_error() == JSON_ERROR_NONE);
    }
}
