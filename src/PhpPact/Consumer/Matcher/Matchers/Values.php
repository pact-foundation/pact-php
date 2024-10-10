<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * Match the values in a map, ignoring the keys
 *
 * @deprecated use EachKey or EachValue
 */
class Values extends AbstractMatcher implements JsonFormattableInterface
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(private array $values)
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'values',
            'value' => $this->values,
        ]);
    }
}
