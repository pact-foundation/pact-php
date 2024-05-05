<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MinTypeFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 * In addition, if the values represent a collection, the length of the actual value is compared against the minimum.
 */
class MinType extends AbstractMatcher
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(
        private array $values,
        private int $min,
    ) {
        parent::__construct();
    }

    public function getType(): string
    {
        return 'type';
    }

    /**
     * @return array<string, int>
     */
    protected function getAttributesData(): array
    {
        return ['min' => $this->min];
    }

    /**
     * @return array<int, mixed>
     */
    public function getValue(): array
    {
        return array_values($this->values);
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new NoGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new MinTypeFormatter();
    }
}
