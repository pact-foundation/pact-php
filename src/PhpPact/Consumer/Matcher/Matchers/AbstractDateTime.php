<?php

namespace PhpPact\Consumer\Matcher\Matchers;

abstract class AbstractDateTime extends GeneratorAwareMatcher
{
    public function __construct(protected string $format, private ?string $value = null)
    {
        parent::__construct();
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return ['format' => $this->format];
    }

    protected function getValue(): ?string
    {
        return $this->value;
    }
}
