<?php

namespace PhpPact\Consumer\Matcher;

class LikeMatcher implements MatcherInterface
{
    /** @var mixed */
    private $value;

    /** @var null|float */
    private $min;

    /** @var null|float */
    private $max;

    public function __construct($value, float $min = null, float $max = null)
    {
        $this->value = $value;
        $this->min   = $min;
        $this->max   = $max;
    }

    public function getMatch(): string
    {
        return 'type';
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return null|float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return null|float
     */
    public function getMax()
    {
        return $this->max;
    }

    public function jsonSerialize()
    {
        $results = [
            'match' => $this->getMatch()
        ];

        if ($this->getMin() !== null) {
            $results['min'] = $this->getMin();
        }

        if ($this->getMax() !== null) {
            $results['max'] = $this->getMax();
        }

        return $results;
    }
}
