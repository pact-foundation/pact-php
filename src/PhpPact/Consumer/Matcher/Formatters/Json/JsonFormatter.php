<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class JsonFormatter implements FormatterInterface
{
    public function format(MatcherInterface $matcher): Attributes
    {
        if (!$matcher instanceof JsonFormattableInterface) {
            throw new MatcherNotSupportedException('Matcher does not support json format');
        }

        return $matcher->formatJson();
    }
}
