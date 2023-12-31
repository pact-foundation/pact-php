<?php

namespace PhpPact\Consumer\Matcher\Matchers;

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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->getFormatter()->format($this, null, array_values($this->values));
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
    protected function getValue(): array
    {
        return array_values($this->values);
    }
}
