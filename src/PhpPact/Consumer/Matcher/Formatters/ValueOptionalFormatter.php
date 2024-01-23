<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ValueOptionalFormatter implements FormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        $data = [
            'pact:matcher:type' => $matcher->getType(),
        ];
        $attributes = $matcher->getAttributes();
        $generator = $matcher instanceof GeneratorAwareInterface ? $matcher->getGenerator() : null;

        if ($generator) {
            return $data + ['pact:generator:type' => $generator->getType()] + $attributes->merge($generator->getAttributes())->getData();
        }

        return $data + $attributes->getData() + ['value' => $matcher->getValue()];
    }
}
