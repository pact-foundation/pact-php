<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;

/**
 * Generates a value that is looked up from the provider state context using the given expression
 *
 * Example expression: /products/${id}
 */
class ProviderState extends AbstractGenerator implements JsonFormattableInterface
{
    public function __construct(private string $expression)
    {
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'ProviderState',
            'expression' => $this->expression,
        ]);
    }
}
