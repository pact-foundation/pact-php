<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;

final class BodyValidator implements BodyValidatorInterface
{
    public const INT_REGEX = '/\d+/';
    public const DEC_REGEX = '/\d+\.\d+/';
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
        Assert::assertTrue((bool) match ($type) {
            'integer' => is_numeric($value) && preg_match(self::INT_REGEX, $value),
            'decimal number' => is_string($value) && preg_match(self::DEC_REGEX, $value),
            'hexadecimal number' => is_string($value) && preg_match(self::HEX_REGEX, $value),
            'random string' => is_string($value),
            'string from the regex' => is_string($value) && preg_match(self::STR_REGEX, $value),
            'date' => is_string($value) && preg_match(self::DATE_REGEX, $value),
            'time' => is_string($value) && preg_match(self::TIME_REGEX, $value),
            'date-time' => is_string($value) && preg_match(self::DATETIME_REGEX, $value),
            'UUID', 'simple UUID', 'lower-case-hyphenated UUID', 'upper-case-hyphenated UUID', 'URN UUID' => Uuid::isValid($value),
            'boolean' => is_bool($value),
            default => false,
        });
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
