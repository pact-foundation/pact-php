<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a random alphanumeric string of the provided length
 */
class RandomString extends AbstractGenerator
{
    public function __construct(private int $size = 10)
    {
    }

    public function getType(): string
    {
        return 'RandomString';
    }

    /**
     * @return array<string, int>
     */
    protected function getAttributesData(): array
    {
        return [
            'size' => $this->size,
        ];
    }
}
