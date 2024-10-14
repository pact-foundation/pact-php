<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 */
class Type extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    public function __construct(private mixed $value)
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'type',
            'value' => $this->value,
        ]);
    }

    public function formatExpression(): Expression
    {
        return new Expression('matching(type, %value%)', ['value' => $this->value]);
    }
}
