<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;

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

    /**
     * @return string|array<string, mixed>
     */
    public function jsonSerialize(): string|array
    {
        $formatter = $this->getFormatter();
        if ($formatter instanceof PluginFormatter) {
            return $formatter->formatContentTypeMatcher($this);
        }

        return parent::jsonSerialize();
    }
}
