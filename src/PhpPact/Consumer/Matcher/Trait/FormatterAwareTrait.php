<?php

namespace PhpPact\Consumer\Matcher\Trait;

use PhpPact\Consumer\Matcher\Model\FormatterInterface;

trait FormatterAwareTrait
{
    private FormatterInterface $formatter;

    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    public function withFormatter(FormatterInterface $formatter): static
    {
        $matcher = clone $this;
        $matcher->setFormatter($formatter);

        return $matcher;
    }
}
