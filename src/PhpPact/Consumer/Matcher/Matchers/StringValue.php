<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\RandomString;

/**
 * There is no matcher for string. We re-use `type` matcher.
 */
class StringValue extends GeneratorAwareMatcher
{
    public function __construct(private ?string $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomString());
        }
    }

    public function getType(): string
    {
        return 'type';
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            'pact:matcher:type' => $this->getType(),
            'value' => $this->getValue() ?? 'some string',
        ];

        if ($this->getGenerator()) {
            return $data + ['pact:generator:type' => $this->getGenerator()->getType()] + $this->getMergedAttributes()->getData();
        }

        return $data;
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): ?string
    {
        return $this->value;
    }
}
