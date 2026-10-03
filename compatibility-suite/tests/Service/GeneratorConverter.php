<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPactTest\CompatibilitySuite\Exception\CompatibilitySuiteException;
use PhpPactTest\CompatibilitySuite\Model\Generator;

final class GeneratorConverter implements GeneratorConverterInterface
{
    public function convert(Generator $generator): MatcherInterface
    {
        $namespace = 'PhpPact\Consumer\Matcher\Generators';
        $class = sprintf('%s\%s', $namespace, $generator->getGenerator());

        $generatorInstance = new $class(...$generator->getGeneratorAttributes());
        if (!$generatorInstance instanceof GeneratorInterface) {
            throw new CompatibilitySuiteException(sprintf('Generator %s is not supported.', $generator->getGenerator()));
        }

        $matcher = new Integer(); // Doesn't matter. Any matcher doesn't require value and accept generator will be fine.
        $matcher->setGenerator($generatorInstance);

        return $matcher;
    }
}
