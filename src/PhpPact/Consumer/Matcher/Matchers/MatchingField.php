<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;

class MatchingField extends AbstractMatcher implements ExpressionFormattableInterface
{
    public function __construct(private string $fieldName)
    {
    }

    public function formatExpression(): Expression
    {
        return new Expression("matching($%fieldName%)", ['fieldName' => $this->fieldName]);
    }
}
