<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a random boolean value
 */
class RandomBoolean extends AbstractGenerator
{
    public function getType(): string
    {
        return 'RandomBoolean';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return [];
    }
}
