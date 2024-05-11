<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\NullValueFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NullValueFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Match if the value is a null value (this is content specific, for JSON will match a JSON null)
 */
class NullValue extends AbstractMatcher
{
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
    public function getValue(): mixed
    {
        return null;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new JsonFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new ExpressionFormatter();
    }
}
