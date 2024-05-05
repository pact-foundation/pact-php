<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\MatchingFieldFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

class MatchingField extends AbstractMatcher
{
    public function __construct(private string $fieldName)
    {
        $this->setFormatter($this->createExpressionFormatter());
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getValue(): string
    {
        return $this->getFieldName();
    }

    public function getType(): string
    {
        return 'matchingField';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function jsonSerialize(): string
    {
        $result = parent::jsonSerialize();
        if (is_array($result)) {
            throw new MatcherNotSupportedException("MatchingField matcher doesn't support json formatter");
        }

        return $result;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        throw new MatcherNotSupportedException("MatchingField matcher doesn't support json formatter");
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new MatchingFieldFormatter();
    }
}
