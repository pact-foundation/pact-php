<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\TypeFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 */
class Type extends AbstractMatcher
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
        return 'type';
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new NoGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new TypeFormatter();
    }
}
