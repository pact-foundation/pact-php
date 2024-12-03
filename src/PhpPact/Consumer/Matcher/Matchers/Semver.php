<?php

namespace PhpPact\Consumer\Matcher\Matchers;

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

    public function __construct(private string $value = '')
    {
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
        return new Expression('matching(semver, %value%)', ['value' => $this->value]);
    }
}
