<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a value that is looked up from the provider state context using the given expression
 *
 * Example expression: /products/${id}
 */
class ProviderState extends AbstractGenerator
{
    public function __construct(private string $expression)
    {
    }

    public function getType(): string
    {
        return 'ProviderState';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return [
            'expression' => $this->expression,
        ];
    }
}
