<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\NotEmptyFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Value must be present and not empty (not null or the empty string)
 */
class NotEmpty extends AbstractMatcher
{
    /**
     * @param object|array<mixed>|string|float|int|bool $value
     */
    public function __construct(private object|array|string|float|int|bool $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return object|array<mixed>|string|float|int|bool
     */
    public function getValue(): object|array|string|float|int|bool
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'notEmpty';
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new NoGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new NotEmptyFormatter();
    }
}
