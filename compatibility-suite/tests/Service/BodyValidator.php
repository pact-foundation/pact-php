<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PHPUnit\Framework\Assert;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;
use Ramsey\Uuid\Uuid;

final class BodyValidator implements BodyValidatorInterface
{
    public const HEX_REGEX = '/[a-fA-F0-9]+/';
    public const STR_REGEX = '/\d{1,8}/';
    public const DATE_REGEX = '/\d{4}-\d{2}-\d{2}/';
    public const TIME_REGEX = '/\d{2}:\d{2}:\d{2}/';
    public const DATETIME_REGEX = '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{1,9}/';

    public function __construct(private BodyStorageInterface $bodyStorage)
    {
    }

    public function validateType(string $path, string $type): void
    {
        $value = $this->getActualValue($path);
        match ($type) {
            'integer' => Assert::assertIsInt($value),
            'decimal number' => Assert::assertIsFloat($value),
            'hexadecimal number' => $this->assertStringMatches(self::HEX_REGEX, $value),
            'random string' => Assert::assertIsString($value),
            'string from the regex' => $this->assertStringMatches(self::STR_REGEX, $value),
            'date' => $this->assertStringMatches(self::DATE_REGEX, $value),
            'time' => $this->assertStringMatches(self::TIME_REGEX, $value),
            'date-time' => $this->assertStringMatches(self::DATETIME_REGEX, $value),
            'UUID', 'simple UUID', 'lower-case-hyphenated UUID', 'upper-case-hyphenated UUID', 'URN UUID' => Assert::assertTrue(Uuid::isValid(TypeCaster::toString($value))),
            'boolean' => Assert::assertIsBool($value),
            default => null,
        };
    }

    private function assertStringMatches(string $pattern, mixed $value): void
    {
        Assert::assertIsString($value);
        Assert::assertMatchesRegularExpression($pattern, $value);
    }

    public function validateValue(string $path, string $value): void
    {
        Assert::assertSame($value, $this->getActualValue($path));
    }

    private function getActualValue(string $path): mixed
    {
        $jsonObject = new JsonObject($this->bodyStorage->getBody(), true);

        return $jsonObject->{$path};
    }
}
