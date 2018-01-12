<?php

namespace PhpPact\Consumer\Matcher;

class RegexMatcher implements MatcherInterface
{
    /** @var mixed */
    private $value;

    /** @var string */
    private $regex;

    public function __construct($value, $regex)
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
