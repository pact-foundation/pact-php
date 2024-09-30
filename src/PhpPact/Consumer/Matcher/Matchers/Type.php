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
