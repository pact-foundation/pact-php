<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MinTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MinTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 * In addition, if the values represent a collection, the length of the actual value is compared against the minimum.
 */
class MinType extends AbstractMatcher
{
    public function __construct(
        private mixed $value,
        private int $min,
        private bool $matchingType = true
    ) {
        if ($min < 0) {
            trigger_error("[WARN] min value to an array matcher can't be less than zero", E_USER_WARNING);
            $this->min = 0;
        }
        parent::__construct();
    }

    public function getType(): string
    {
        return 'type';
    }

    /**
     * @return array<string, int>
     */
    protected function getAttributesData(): array
    {
        return ['min' => $this->min];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getMin(): int
    {
        return $this->min;
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
