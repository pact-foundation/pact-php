<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Matcher\MatcherInterface;

/**
 * Response expectation that would be in response to a Consumer request from the Provider.
 * Class ProviderResponse
 */
class ProviderResponse implements \JsonSerializable
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var null|string[]
     */
    private $headers;

    /**
     * @var null|array
     */
    private $body;

    /**
     * @var null|MatcherInterface[]
     */
    private $matchingRules;

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return ProviderResponse
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     *
     * @return ProviderResponse
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $header
     * @param string $value
     *
     * @return ProviderResponse
     */
    public function addHeader(string $header, string $value): self
    {
        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param iterable $body
     *
     * @return ProviderResponse
     */
    public function setBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return null|MatcherInterface[]
     */
    public function getMatchingRules()
    {
        return $this->matchingRules;
    }

    /**
     * Manually add a matcher to the response.
     *
     * @param string           $pattern JSON path
     * @param MatcherInterface $matcher matcher object to represent the type
     *
     * @return self
     */
    public function addMatchingRule(string $pattern, MatcherInterface $matcher): self
    {
        $this->matchingRules[$pattern] = $matcher;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $results = [
            'status' => $this->getStatus()
        ];

        if ($this->getHeaders() !== null) {
            $results['headers'] = $this->getHeaders();
        }

        if ($this->getMatchingRules() !== null) {
            $results['matchingRules'] = $this->getMatchingRules();
        }

        if ($this->getBody() !== null) {
            $results['body'] = $this->getBody();
        }

        return $results;
    }
}
