<?php

namespace PhpPact\Consumer\Matcher\Model\Matcher;

use PhpPact\Consumer\Matcher\Model\Attributes;

interface JsonFormattableInterface
{
    public function formatJson(): Attributes;
}
