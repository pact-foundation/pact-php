<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\ValueOptionalFormatter;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

abstract class AbstractMatcher implements MatcherInterface
{
    private FormatterInterface $formatter;

    public function __construct()
    {
        $this->formatter = new ValueOptionalFormatter();
    }

    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

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
