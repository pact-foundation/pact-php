<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

abstract class AbstractExpressionFormatter implements ExpressionFormatterInterface
{
    protected function normalize(mixed $value): string
    {
        if (is_string($value) && str_contains($value, "'")) {
            throw new InvalidValueException(sprintf('String value "%s" should not contains single quote', $value));
        }
        return match (gettype($value)) {
            'string' => sprintf("'%s'", $value),
            'boolean' => $value ? 'true' : 'false',
            'integer' => (string) $value,
            'double' => (string) $value,
            'NULL' => 'null',
            default => throw new InvalidValueException(sprintf("Expression doesn't support value of type %s", gettype($value))),
        };
    }

    protected function getMatcherNotSupportedException(MatcherInterface $matcher): MatcherNotSupportedException
    {
        return new MatcherNotSupportedException(sprintf('Matcher %s is not supported by %s', $matcher->getType(), static::class));
    }
}
