<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Consumer\Matcher\Trait\FormatterAwareTrait;

abstract class AbstractMatcher implements MatcherInterface
{
    use FormatterAwareTrait;

    /**
     * @return string|array<string, mixed>
     */
    public function jsonSerialize(): string|array
    {
        return $this->getFormatter()->format($this);
    }

    public function getAttributes(): Attributes
    {
        return new Attributes($this, $this->getAttributesData());
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function getAttributesData(): array;
}
