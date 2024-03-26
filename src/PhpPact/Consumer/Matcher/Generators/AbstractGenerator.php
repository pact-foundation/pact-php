<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

abstract class AbstractGenerator implements GeneratorInterface
{
    public function getAttributes(): Attributes
    {
        return new Attributes($this, $this->getAttributesData());
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function getAttributesData(): array;
}
