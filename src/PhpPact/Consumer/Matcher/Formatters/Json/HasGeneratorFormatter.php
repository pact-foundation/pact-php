<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class HasGeneratorFormatter extends NoGeneratorFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        if (!$matcher instanceof GeneratorAwareInterface) {
            throw new MatcherNotSupportedException(sprintf('Matcher %s is not supported by %s', $matcher->getType(), self::class));
        }

        $generator = $matcher->getGenerator();

        if ($generator) {
            return [
                'pact:matcher:type' => $matcher->getType(),
                'pact:generator:type' => $generator->getType(),
                ...$matcher->getAttributes()->merge($generator->getAttributes())->getData(),
            ];
        }

        return parent::format($matcher);
    }
}
