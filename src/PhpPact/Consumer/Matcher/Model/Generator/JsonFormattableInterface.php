<?php

namespace PhpPact\Consumer\Matcher\Model\Generator;

use PhpPact\Consumer\Matcher\Model\Attributes;

interface JsonFormattableInterface
{
    public function formatJson(): Attributes;
}
