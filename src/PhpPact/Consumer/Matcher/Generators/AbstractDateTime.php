<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

abstract class AbstractDateTime implements GeneratorInterface
{
    public function __construct(private ?string $format = null, private ?string $expression = null)
    {
    }

    public function jsonSerialize(): object
    {
        $data = ['pact:generator:type' => $this->getType()];

        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        if ($this->expression !== null) {
            $data['expression'] = $this->expression;
        }

        return (object) $data;
    }

    abstract protected function getType(): string;
}
