<?php

namespace PhpPact\Consumer\Matcher\Model;

use JsonSerializable;

interface MatcherInterface extends JsonSerializable
{
    public function getType(): string;

    public function getAttributes(): Attributes;
}
