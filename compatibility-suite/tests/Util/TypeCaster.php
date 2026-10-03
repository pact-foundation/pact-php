<?php

namespace PhpPactTest\CompatibilitySuite\Util;

use PhpPactTest\CompatibilitySuite\Exception\CompatibilitySuiteException;

final class TypeCaster
{
    public static function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (null === $value) {
            return '';
        }

        throw new CompatibilitySuiteException(sprintf('Cannot convert value of type %s to string.', get_debug_type($value)));
    }

    public static function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value) || is_bool($value) || (is_string($value) && is_numeric($value))) {
            return (int) $value;
        }

        throw new CompatibilitySuiteException(sprintf('Cannot convert value of type %s to int.', get_debug_type($value)));
    }
}
