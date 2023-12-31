<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\ValueOptionalFormatter;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\FormatterAwareInterface;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

abstract class AbstractMatcher implements MatcherInterface, FormatterAwareInterface
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
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->getFormatter()->format($this, null, $this->getValue());
    }

    public function getAttributes(): Attributes
    {
        return new Attributes($this, $this->getAttributesData());
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function getAttributesData(): array;

    abstract protected function getValue(): mixed;
}
