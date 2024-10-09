<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * This checks if the type of the value is a number with decimal places.
 */
class Decimal extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;

    public function __construct(private ?float $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomDecimal());
        }
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'decimal',
            'value' => $this->value,
        ]));
    }

    public function formatExpression(): Expression
    {
        if (!is_float($this->value)) {
            throw new InvalidValueException(sprintf("Decimal matching expression doesn't support value of type %s", gettype($this->value)));
        }
        return new Expression('matching(decimal, %value%)', ['value' => $this->value]);
    }
}
