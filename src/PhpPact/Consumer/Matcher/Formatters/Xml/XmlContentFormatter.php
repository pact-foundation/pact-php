<?php

namespace PhpPact\Consumer\Matcher\Formatters\Xml;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class XmlContentFormatter implements FormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        $generator = $matcher instanceof GeneratorAwareInterface ? $matcher->getGenerator() : null;
        $data = [
            'content' => $matcher->getValue(),
            'matcher' => [
                'pact:matcher:type' => $matcher->getType(),
                ...$matcher->getAttributes()->merge($generator ? $generator->getAttributes() : new Attributes($matcher))->getData(),
            ],
        ];

        if ($generator) {
            $data['pact:generator:type'] = $generator->getType();
        }

        return $data;
    }
}
