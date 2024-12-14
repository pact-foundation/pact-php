<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PHPUnit\Framework\Assert;
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
            'hexadecimal number' => Assert::assertIsString($value) && Assert::assertMatchesRegularExpression(self::HEX_REGEX, $value),
            'random string' => Assert::assertIsString($value),
            'string from the regex' => Assert::assertIsString($value) && Assert::assertMatchesRegularExpression(self::STR_REGEX, $value),
            'date' => Assert::assertIsString($value) && Assert::assertMatchesRegularExpression(self::DATE_REGEX, $value),
            'time' => Assert::assertIsString($value) && Assert::assertMatchesRegularExpression(self::TIME_REGEX, $value),
            'date-time' => Assert::assertIsString($value) && Assert::assertMatchesRegularExpression(self::DATETIME_REGEX, $value),
            'UUID', 'simple UUID', 'lower-case-hyphenated UUID', 'upper-case-hyphenated UUID', 'URN UUID' => Assert::assertTrue(Uuid::isValid($value)),
            'boolean' => Assert::assertIsBool($value),
            default => null,
        };
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
