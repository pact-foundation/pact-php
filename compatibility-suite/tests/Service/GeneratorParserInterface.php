<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\Generator;

interface GeneratorParserInterface
{
    /**
     * @return array<int, Generator>
     */
    public function parse(string $value): array;
}
