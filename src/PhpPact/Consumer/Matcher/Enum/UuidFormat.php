<?php

namespace PhpPact\Consumer\Matcher\Enum;

enum UuidFormat: string
{
    case SIMPLE = 'simple';
    case LOWER_CASE_HYPHENATED = 'lower-case-hyphenated';
    case UPPER_CASE_HYPHENATED = 'upper-case-hyphenated';
    case URN = 'URN';
}
