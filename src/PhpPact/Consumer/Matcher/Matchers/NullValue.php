<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * Match if the value is a null value (this is content specific, for JSON will match a JSON null)
 */
class NullValue extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'null',
        ]);
    }

    public function formatExpression(): Expression
    {
        return new Expression('matching(type, null)');
    }
}
