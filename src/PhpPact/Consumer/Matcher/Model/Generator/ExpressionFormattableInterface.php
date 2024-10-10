<?php

namespace PhpPact\Consumer\Matcher\Model\Generator;

use PhpPact\Consumer\Matcher\Model\Expression;

interface ExpressionFormattableInterface
{
    public function formatExpression(): Expression;
}
