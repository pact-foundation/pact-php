<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\StringValueFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\StringValueFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

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
        parent::__construct();
    }

    public function getType(): string
    {
        return 'type';
    }

    /**
     * @return string|array<string, mixed>
     */
    public function jsonSerialize(): string|array
    {
        return $this->getFormatter()->format($this);
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): string
    {
        return $this->value ?? self::DEFAULT_VALUE;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new JsonFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new ExpressionFormatter();
    }
}
