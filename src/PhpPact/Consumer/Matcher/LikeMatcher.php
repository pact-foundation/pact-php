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

    public function __construct($value = null, float $min = null, float $max = null)
    {
        $this->value = $value;
        $this->min   = $min;
        $this->max   = $max;
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

    /** @inheritdoc */
    public function jsonSerialize()
    {
        if (\is_array($this->getValue())) {
            $data = [
                'contents'   => $this->value,
                'json_class' => 'Pact::ArrayLike',
                'min'        => $this->getMin() ?? 1
            ];

            if ($this->getMax() !== null) {
                $data['max'] = $this->getMax();
            }
        } else {
            return [
                'contents'   => $this->value,
                'json_class' => 'Pact::SomethingLike'
            ];
        }

        return $data;
    }
}
