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
    /**
     * @param object|array<mixed>|string|float|int|bool|null $value
     */
    public function __construct(private object|array|string|float|int|bool|null $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return object|array<mixed>|string|float|int|bool|null
     */
    public function getValue(): object|array|string|float|int|bool|null
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
