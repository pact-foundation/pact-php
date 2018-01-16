<?php

namespace PhpPact\Consumer\Matcher;

/**
 * Class RegexMatcher
 * @package PhpPact\Consumer\Matcher
 */
class RegexMatcher implements MatcherInterface
{
    /** @var mixed */
    private $value;

    /** @var string */
    private $regex;

    /**
     * RegexMatcher constructor.
     * @param int|float|string $value value that will be matched
     * @param string $regex regex to be used
     */
    public function __construct($value, string $regex)
    {
        $this->value = $value;
        $this->regex = $regex;
    }

    /** @inheritdoc */
    public function getMatch(): string
    {
        return 'regex';
    }

    /** @inheritdoc */
    public function getValue()
    {
        return $this->value;
    }

    /** @inheritdoc */
    public function jsonSerialize()
    {
        return [
            'match' => $this->getMatch(),
            'regex' => $this->regex
        ];
    }
}
