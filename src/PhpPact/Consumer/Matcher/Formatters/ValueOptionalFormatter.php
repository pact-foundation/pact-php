<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ValueOptionalFormatter implements FormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher, ?GeneratorInterface $generator, mixed $value): array
    {
        $data = [
            'pact:matcher:type' => $matcher->getType(),
        ];

        if ($generator) {
            return $data + ['pact:generator:type' => $generator->getType()] + $matcher->getAttributes()->merge($generator->getAttributes())->getData();
        }

        return $data + $matcher->getAttributes()->getData() + ['value' => $value];
    }
}
