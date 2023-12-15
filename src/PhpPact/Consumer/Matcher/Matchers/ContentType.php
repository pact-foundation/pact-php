<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Match binary data by its content type (magic file check)
 */
class ContentType implements MatcherInterface
{
    public function __construct(private string $contentType)
    {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'value'             => $this->contentType,
            'pact:matcher:type' => $this->getType(),
        ];
    }

    public function getType(): string
    {
        return 'contentType';
    }
}
