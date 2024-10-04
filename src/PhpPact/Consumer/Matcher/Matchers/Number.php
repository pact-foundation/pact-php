<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * This checks if the type of the value is a number.
 */
class Number extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;

    public function __construct(private int|float|null $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomInt());
        }
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'number',
            'value' => $this->value,
        ]));
    }

    public function formatExpression(): Expression
    {
        if (null === $this->value) {
            throw new InvalidValueException(sprintf("Number matching expression doesn't support value of type %s", gettype($this->value)));
        }
        return new Expression('matching(number, %value%)', ['value' => $this->value]);
    }
}
