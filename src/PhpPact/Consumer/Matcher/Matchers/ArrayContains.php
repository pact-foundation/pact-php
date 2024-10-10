<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * Checks if all the variants are present in an array.
 */
class ArrayContains extends AbstractMatcher implements JsonFormattableInterface
{
    /**
     * @param array<mixed> $variants
     */
    public function __construct(private array $variants)
    {
        if (empty($variants)) {
            throw new InvalidValueException('Variants should not be empty');
        }
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'arrayContains',
            'variants' => array_values($this->variants),
            'value' => array_values($this->variants),
        ]);
    }
}
