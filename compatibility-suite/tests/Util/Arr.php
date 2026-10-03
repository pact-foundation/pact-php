<?php

namespace PhpPactTest\CompatibilitySuite\Util;

final class Arr
{
    /**
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     */
    public static function sub(array $array, string|int ...$keys): array
    {
        $value = self::value($array, ...$keys);

        return (array) $value;
    }

    /**
     * @param array<array-key, mixed> $array
     */
    public static function str(array $array, string|int ...$keys): string
    {
        return TypeCaster::toString(self::value($array, ...$keys));
    }

    /**
     * @param array<array-key, mixed> $array
     */
    public static function int(array $array, string|int ...$keys): int
    {
        return TypeCaster::toInt(self::value($array, ...$keys));
    }

    /**
     * @param array<array-key, mixed> $array
     */
    private static function value(array $array, string|int ...$keys): mixed
    {
        $value = $array;
        foreach ($keys as $key) {
            $value = is_array($value) ? ($value[$key] ?? null) : null;
        }

        return $value;
    }
}
