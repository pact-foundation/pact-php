<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPactTest\CompatibilitySuite\Model\Generator;

interface GeneratorConverterInterface
{
    public function convert(Generator $generator): MatcherInterface;
}
