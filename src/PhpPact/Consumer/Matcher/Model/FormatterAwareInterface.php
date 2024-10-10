<?php

namespace PhpPact\Consumer\Matcher\Model;

interface FormatterAwareInterface
{
    public function setFormatter(FormatterInterface $formatter): void;

    public function getFormatter(): FormatterInterface;

    public function withFormatter(FormatterInterface $formatter): static;
}
