<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MinMaxTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MinMaxTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 * In addition, if the values represent a collection, the length of the actual value is compared against the minimum and maximum.
 */
class MinMaxType extends AbstractMatcher
{
    public function __construct(
        private mixed $value,
        private int $min,
        private int $max,
    ) {
        if ($min < 0) {
            trigger_error("[WARN] min value to an array matcher can't be less than zero", E_USER_WARNING);
            $this->min = 0;
        }
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
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'type';
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
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
