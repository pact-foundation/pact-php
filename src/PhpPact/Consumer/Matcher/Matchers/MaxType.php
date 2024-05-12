<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MaxTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MaxTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 * In addition, if the values represent a collection, the length of the actual value is compared against the maximum.
 */
class MaxType extends AbstractMatcher
{
    public function __construct(
        private mixed $value,
        private int $max,
        private bool $matchingType = true
    ) {
        if ($max < 0) {
            trigger_error("[WARN] max value to an array matcher can't be less than zero", E_USER_WARNING);
            $this->max = 0;
        }
        parent::__construct();
    }

    /**
     * @return array<string, int>
     */
    protected function getAttributesData(): array
    {
        return ['max' => $this->max];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'type';
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function isMatchingType(): bool
    {
        return $this->matchingType;
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
