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
    public function parse(&$body, string $jsonPath = '$.body')
    {
        if ($body instanceof \stdClass) {
            $body = (array) $body;
        }

        if (\is_array($body)) {
            foreach ($body as $key => &$item) {
                $path = $jsonPath;

                // If not an associative array, set the key for the next item.
                if ($body !== \array_values($body)) {
                    $path .= ".{$key}";
                }

                if ($item instanceof MatcherInterface) {
                    if ($item->getValue() instanceof \stdClass) {
                        $value = (array) $item->getValue();
                    } else {
                        $value = $item->getValue();
                    }

                    if (\is_array($value)) {
                        $path .= '[*]';

                        // If the item is an associative array, make sure each item in that array is matched.
                        if ($value !== \array_values($value)) {
                            $path .= '.[*]';
                        }
                    }

                    $this->addMatchingRule($item, $path);
                    $item = $value;
                } else {
                    $this->parse($item, $path);
                }
            }
        }

        return $this->matchingRules;
    }

    /**
     * Add a matching rule to the array stack.
     *
     * @param MatcherInterface $matchingRule
     * @param string           $path
     *
     * @return MatchParser
     */
    private function addMatchingRule(MatcherInterface $matchingRule, string $path): self
    {
        $this->matchingRules[$path] = $matchingRule;

        return $this;
    }
}
