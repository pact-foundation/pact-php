<?php

namespace PhpPact\Consumer\Matcher\Matchers;

class MatchAll extends CombinedMatchers
{
    public function getType(): string
    {
        return 'matchAll';
    }
}
