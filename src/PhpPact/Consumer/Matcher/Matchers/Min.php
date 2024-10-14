<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 * In addition, if the values represent a collection, the length of the actual value is compared against the minimum.
 */
class Min extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    public function __construct(private int $min)
    {
        if ($min < 0) {
            trigger_error("[WARN] min value to an array matcher can't be less than zero", E_USER_WARNING);
            $this->min = 0;
        }
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'type',
            'min' => $this->min,
            'value' => array_fill(0, max($this->min, 1), null),
        ]);
    }

    public function formatExpression(): Expression
    {
        return new Expression(sprintf('atLeast(%u)', $this->min));
    }
}
