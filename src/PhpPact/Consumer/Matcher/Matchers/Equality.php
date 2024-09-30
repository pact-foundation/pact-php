<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\EqualityFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This is the default matcher, and relies on the equals operator
 */
class Equality extends AbstractMatcher
{
    public function __construct(private mixed $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'equality';
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new NoGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new EqualityFormatter();
    }
}
