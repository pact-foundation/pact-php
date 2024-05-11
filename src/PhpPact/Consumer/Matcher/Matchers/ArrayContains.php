<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Checks if all the variants are present in an array.
 */
class ArrayContains extends AbstractMatcher
{
    /**
     * @param array<mixed> $variants
     */
    public function __construct(private array $variants)
    {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getAttributesData(): array
    {
        return ['variants' => $this->getValue()];
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return array_values($this->variants);
    }

    public function getType(): string
    {
        return 'arrayContains';
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new NoGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        throw new MatcherNotSupportedException("ArrayContains matcher doesn't support expression formatter");
    }
}
