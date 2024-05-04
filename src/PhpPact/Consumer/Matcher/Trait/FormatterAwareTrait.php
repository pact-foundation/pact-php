<?php

namespace PhpPact\Consumer\Matcher\Trait;

use PhpPact\Consumer\Matcher\Formatters\ValueOptionalFormatter;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;

trait FormatterAwareTrait
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
}
