<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a random string from the provided regular expression
 */
class Regex extends AbstractGenerator
{
    public function __construct(private string $regex)
    {
    }

    public function getType(): string
    {
        return 'Regex';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return [
            'regex' => $this->regex,
        ];
    }
}
