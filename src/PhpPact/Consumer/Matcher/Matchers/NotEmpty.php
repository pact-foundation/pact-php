<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * Value must be present and not empty (not null or the empty string)
 */
class NotEmpty extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    public function __construct(private mixed $value)
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'notEmpty',
            'value' => $this->value,
        ]);
    }

    public function formatExpression(): Expression
    {
        return new Expression('notEmpty(%value%)', ['value' => $this->value]);
    }
}
