<?php

namespace PhpPact\Consumer\Matcher\Trait;

use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\Generator\ExpressionFormattableInterface as GeneratorExpressionFormattableInterface;

trait ExpressionFormattableTrait
{
    public function mergeExpression(Expression $expression): Expression
    {
        if ($this instanceof GeneratorAwareInterface) {
            $generator = $this->getGenerator();
            if ($generator instanceof GeneratorExpressionFormattableInterface) {
                $generated = $generator->formatExpression();

                return new Expression(
                    str_replace('%value%', $generated->getFormat(), $expression->getFormat()),
                    [
                        ...$expression->getValues(),
                        ...$generated->getValues(),
                    ]
                );
            }
        }
        return $expression;
    }
}
