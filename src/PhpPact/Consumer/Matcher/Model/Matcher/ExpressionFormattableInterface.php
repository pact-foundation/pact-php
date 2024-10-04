<?php

namespace PhpPact\Consumer\Matcher\Model\Matcher;

use PhpPact\Consumer\Matcher\Model\Expression;

interface ExpressionFormattableInterface
{
    public function formatExpression(): Expression;
}
