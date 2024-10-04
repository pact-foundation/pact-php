<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Json\JsonFormatter;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Consumer\Matcher\Trait\FormatterAwareTrait;

abstract class AbstractMatcher implements MatcherInterface
{
    use FormatterAwareTrait;

    public function __construct()
    {
        $this->setFormatter(new JsonFormatter());
    }

    public function jsonSerialize(): mixed
    {
        return $this->getFormatter()->format($this);
    }
}
