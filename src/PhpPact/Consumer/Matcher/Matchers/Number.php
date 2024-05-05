<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\NumberFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This checks if the type of the value is a number.
 */
class Number extends GeneratorAwareMatcher
{
    public function __construct(private int|float|null $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomInt());
        }
        parent::__construct();
    }

    public function getType(): string
    {
        return 'number';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): int|float|null
    {
        return $this->value;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new HasGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new NumberFormatter();
    }
}
