<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

abstract class AbstractDateTime implements GeneratorInterface, JsonFormattableInterface
{
    public function __construct(private ?string $format = null, private ?string $expression = null)
    {
    }

    public function formatJson(): Attributes
    {
        $data = [
            'pact:generator:type' => $this->getType(),
        ];
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        if ($this->expression !== null) {
            $data['expression'] = $this->expression;
        }

        return new Attributes($data);
    }

    abstract protected function getType(): string;
}
