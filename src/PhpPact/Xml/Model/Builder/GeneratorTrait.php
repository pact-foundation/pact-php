<?php

namespace PhpPact\Xml\Model\Builder;

use PhpPact\Xml\Model\Matcher\Generator;

trait GeneratorTrait
{
    public function generatorType(string $type): callable
    {
        return fn (Generator $generator) => $generator->setType($type);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function generatorOptions(array $options): callable
    {
        return fn (Generator $generator) => $generator->setOptions($options);
    }
}
