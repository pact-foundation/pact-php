<?php

namespace PhpPact\Consumer\Matcher\Model;

interface FormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher, ?GeneratorInterface $generator, mixed $value): array;
}
