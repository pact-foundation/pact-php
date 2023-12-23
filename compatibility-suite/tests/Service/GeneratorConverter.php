<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPactTest\CompatibilitySuite\Model\Generator;

final class GeneratorConverter implements GeneratorConverterInterface
{
    public function convert(Generator $generator): MatcherInterface
    {
        $namespace = 'PhpPact\Consumer\Matcher\Generators';
        $class = sprintf('%s\%s', $namespace, $generator->getGenerator());

        $matcher = new Integer(); // Doesn't matter. Any matcher doesn't require value and accept generator will be fine.
        $matcher->setGenerator(new $class(...$generator->getGeneratorAttributes()));

        return $matcher;
    }
}
