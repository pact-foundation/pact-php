<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * Match if the value is a boolean value (booleans and the string values `true` and `false`)
 */
class Boolean extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;

    public function __construct(private bool $value = false)
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'boolean',
            'value' => $this->value,
        ]));
    }

    public function formatExpression(): Expression
    {
        return new Expression('matching(boolean, %value%)', ['value' => $this->value]);
    }
}
