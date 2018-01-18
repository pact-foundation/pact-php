<?php

namespace PhpPact\Consumer\Matcher;

/**
 * Generate matching rules from a request or response body.
 * Class MatchParser
 */
class MatchParser
{
    /** @var MatcherInterface[] */
    private $matchingRules;

    /**
     * Generate matching rules from a request or response body.
     *
     * @param array|float|int|string $body
     * @param string                 $jsonPath
     *
     * @return MatcherInterface[]
     */
    public function parse(&$body, string &$jsonPath = '$.body')
    {
        if (\is_array($body)) {
            foreach ($body as $key => &$item) {
                if (\is_int($key)) {
                    $path = "{$jsonPath}[*]";
                } else {
                    $path = "{$jsonPath}.{$key}";
                }

                if ($item instanceof MatcherInterface) {
                    $this->parseMatcher($item, $path);

                    $item = $item->getValue();
                } else {
                    $this->parse($item, $path);
                }
            }
        }

        return $this->matchingRules;
    }

    /**
     * If the matcher has children, add a matcher pattern for each.
     *
     * @param MatcherInterface $matcher
     * @param string           $jsonPath
     */
    private function parseMatcher(MatcherInterface $matcher, string $jsonPath)
    {
        if (\is_array($matcher->getValue())) {
            foreach ($matcher->getValue() as $key => $value) {
                if (\is_int($key)) {
                    $path = "{$jsonPath}[*]";
                } else {
                    $path = "{$jsonPath}.{$key}";
                }

                $this->addMatchingRule($path, $matcher);
            }
        } else {
            $this->addMatchingRule($jsonPath, $matcher);
        }
    }

    /**
     * Add a matching rule to the array stack.
     *
     * @param string           $path
     * @param MatcherInterface $matchingRule
     *
     * @return MatchParser
     */
    private function addMatchingRule(string $path, MatcherInterface $matchingRule): self
    {
        $this->matchingRules[$path] = $matchingRule;

        return $this;
    }
}
