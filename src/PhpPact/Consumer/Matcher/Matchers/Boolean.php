<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
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

    public function __construct(private ?bool $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomBoolean());
        }
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
        if (!is_bool($this->value)) {
            throw new InvalidValueException(sprintf("Boolean matching expression doesn't support value of type %s", gettype($this->value)));
        }

        return new Expression('matching(boolean, %value%)', ['value' => $this->value]);
    }
}
