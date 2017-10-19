<?php

namespace PhpPact\Mocks\MockHttpService\Models;

use PhpPact\Matchers\Rules\MatchingRule;

interface IHttpMessage
{
    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     * @return mixed
     */
    public function setBody($body);

    /**
     * @return bool
     */
    public function shouldSerializeBody();

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param array $headers
     * @return mixed
     */
    public function setHeaders($headers);

    /**
     * Return the header value for Content-Type
     *
     * False is returned if not set
     *
     * @return mixed|bool
     */
    public function getContentType();

    /**
     * Get the matching rules to run over JSON Path
     *
     * @return array
     */
    public function getMatchingRules();

    /**
     * Set an array filled with MatchingRule
     * @param $matchingRules
     * @return mixed
     */
    public function setMatchingRules($matchingRules);

    /**
     * Add a single matching rule
     *
     * @param MatchingRule $matchingRule
     * @return mixed
     */
    public function addMatchingRule(MatchingRule $matchingRule);
}
