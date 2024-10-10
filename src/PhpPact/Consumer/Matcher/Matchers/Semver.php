<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * Value must be valid based on the semver specification
 */
class Semver extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;

    public function __construct(private ?string $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new Regex('\d+\.\d+\.\d+'));
        }
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'semver',
            'value' => $this->value,
        ]));
    }

    public function formatExpression(): Expression
    {
        if (!is_string($this->value)) {
            throw new InvalidValueException(sprintf("Semver matching expression doesn't support value of type %s", gettype($this->value)));
        }
        return new Expression('matching(semver, %value%)', ['value' => $this->value]);
    }
}
