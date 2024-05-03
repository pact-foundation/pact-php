<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;

class MatchingField extends AbstractMatcher
{
    public const MATCHER_NOT_SUPPORTED_EXCEPTION_MESSAGE = 'MatchingField matcher only work with plugin';

    public function __construct(private string $fieldName)
    {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getValue(): mixed
    {
        throw new MatcherNotSupportedException(self::MATCHER_NOT_SUPPORTED_EXCEPTION_MESSAGE);
    }

    public function getType(): string
    {
        throw new MatcherNotSupportedException(self::MATCHER_NOT_SUPPORTED_EXCEPTION_MESSAGE);
    }

    protected function getAttributesData(): array
    {
        throw new MatcherNotSupportedException(self::MATCHER_NOT_SUPPORTED_EXCEPTION_MESSAGE);
    }

    public function jsonSerialize(): string
    {
        $result = parent::jsonSerialize();
        if (is_array($result)) {
            throw new MatcherNotSupportedException(self::MATCHER_NOT_SUPPORTED_EXCEPTION_MESSAGE);
        }

        return $result;
    }
}
