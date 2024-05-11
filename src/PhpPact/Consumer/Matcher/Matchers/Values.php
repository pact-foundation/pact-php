<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Match the values in a map, ignoring the keys
 *
 * @deprecated use EachKey or EachValue
 */
class Values extends AbstractMatcher
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(private array $values)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    public function getType(): string
    {
        return 'values';
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new NoGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        throw new MatcherNotSupportedException("Values matcher doesn't support expression formatter");
    }
}
