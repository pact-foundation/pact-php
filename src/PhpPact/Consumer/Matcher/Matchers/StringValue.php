<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\ValueRequiredFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;

/**
 * There is no matcher for string. We re-use `type` matcher.
 */
class StringValue extends GeneratorAwareMatcher
{
    public const DEFAULT_VALUE = 'some string';

    public function __construct(private ?string $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomString());
        }
        $this->setFormatter(new ValueRequiredFormatter());
    }

    public function getType(): string
    {
        return 'type';
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->getFormatter()->format($this, $this->getGenerator(), $this->getValue());
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): string
    {
        return $this->value ?? self::DEFAULT_VALUE;
    }
}
