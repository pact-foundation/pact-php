<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * Match the values in a map, ignoring the keys
 */
class Values extends AbstractMatcher
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(private array $values)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    public function getType(): string
    {
        return 'values';
    }
}
