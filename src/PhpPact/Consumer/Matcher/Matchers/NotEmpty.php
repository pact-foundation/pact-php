<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;

/**
 * Value must be present and not empty (not null or the empty string)
 */
class NotEmpty extends AbstractMatcher
{
    /**
     * @param object|array<mixed>|string|float|int|bool $value
     */
    public function __construct(private object|array|string|float|int|bool $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return object|array<mixed>|string|float|int|bool
     */
    public function getValue(): object|array|string|float|int|bool
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'notEmpty';
    }

    /**
     * @return string|array<string, mixed>
     */
    public function jsonSerialize(): string|array
    {
        $formatter = $this->getFormatter();
        if ($formatter instanceof PluginFormatter) {
            return $formatter->formatNotEmptyMatcher($this);
        }

        return parent::jsonSerialize();
    }
}
