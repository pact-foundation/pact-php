<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\ExpressionFormattableTrait;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * This checks if the type of the value is an integer.
 */
class Integer extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;
    use ExpressionFormattableTrait;

    public function __construct(private int $value = 13)
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'integer',
            'value' => $this->value,
        ]));
    }

    public function formatExpression(): Expression
    {
        return $this->mergeExpression(new Expression('matching(integer, %value%)', ['value' => $this->value]));
    }
}
