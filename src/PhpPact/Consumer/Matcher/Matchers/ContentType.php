<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * Match binary data by its content type (magic file check)
 */
class ContentType extends AbstractMatcher
{
    public function __construct(private string $contentType)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): string
    {
        return $this->contentType;
    }

    public function getType(): string
    {
        return 'contentType';
    }
}
