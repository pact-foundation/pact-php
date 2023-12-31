<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\MinimalFormatter;

/**
 * Match if the value is a null value (this is content specific, for JSON will match a JSON null)
 */
class NullValue extends AbstractMatcher
{
    public function __construct()
    {
        $this->setFormatter(new MinimalFormatter());
    }

    public function getType(): string
    {
        return 'null';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @todo Change return type to `null`
     */
    protected function getValue(): mixed
    {
        return null;
    }
}
