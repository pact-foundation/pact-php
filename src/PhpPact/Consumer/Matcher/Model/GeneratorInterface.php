<?php

namespace PhpPact\Consumer\Matcher\Model;

interface GeneratorInterface
{
    public function getType(): string;

    public function getAttributes(): Attributes;
}
