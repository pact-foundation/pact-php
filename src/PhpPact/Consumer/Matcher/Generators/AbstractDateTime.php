<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

abstract class AbstractDateTime implements GeneratorInterface
{
    public function __construct(private ?string $format = null, private ?string $expression = null)
    {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        $data = ['pact:generator:type' => $this->getType()];

        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        if ($this->expression !== null) {
            $data['expression'] = $this->expression;
        }

        return $data;
    }

    abstract protected function getType(): string;
}
