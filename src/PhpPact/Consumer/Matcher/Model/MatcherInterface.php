<?php

namespace PhpPact\Consumer\Matcher\Model;

use JsonSerializable;

interface MatcherInterface extends JsonSerializable, FormatterAwareInterface
{
    public function getType(): string;

    public function getAttributes(): Attributes;

    public function getValue(): mixed;
}
