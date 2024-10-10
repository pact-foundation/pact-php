<?php

namespace PhpPact\Consumer\Matcher\Trait;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface as GeneratorJsonFormattableInterface;

trait JsonFormattableTrait
{
    public function mergeJson(Attributes $attributes): Attributes
    {
        if ($this instanceof GeneratorAwareInterface) {
            $generator = $this->getGenerator();
            if ($generator instanceof GeneratorJsonFormattableInterface) {
                return $generator->formatJson()->merge($attributes);
            }
        }
        return $attributes;
    }
}
