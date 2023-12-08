<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a value that is looked up from the provider state context using the given expression
 *
 * Example expression: /products/${id}
 */
class ProviderState implements GeneratorInterface
{
    public function __construct(private string $expression)
    {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'expression'          => $this->expression,
            'pact:generator:type' => 'ProviderState',
        ];
    }
}
