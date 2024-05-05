<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\DecimalFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This checks if the type of the value is a number with decimal places.
 */
class Decimal extends GeneratorAwareMatcher
{
    public function __construct(private ?float $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomDecimal());
        }
        parent::__construct();
    }

    public function getType(): string
    {
        return 'decimal';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new HasGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new DecimalFormatter();
    }
}
