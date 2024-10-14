<?php

namespace PhpPact\Consumer\Matcher\Model;

use JsonSerializable;
use PhpPact\Consumer\Matcher\Exception\InvalidValueException;

class Expression implements JsonSerializable
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(private string $format, private array $values = [])
    {
        $this->setValues($values);
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function jsonSerialize(): string
    {
        return $this->format();
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function format(): string
    {
        return strtr(
            $this->format,
            array_combine(
                array_map(fn (string $key) => "%{$key}%", array_keys($this->values)),
                array_map(fn (mixed $value) => $this->normalize($value), array_values($this->values)),
            )
        );
    }

    /**
     * @param array<string, mixed> $values
     */
    private function setValues(array $values): void
    {
        $this->values = [];
        foreach ($values as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    private function setValue(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    private function normalize(mixed $value): string
    {
        if (is_string($value)) {
            $value = addcslashes($value, "'");
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
}
