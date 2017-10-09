<?php

namespace PhpPact\Extensions;

class StringExtensions
{
    public static function ToLowerSnakeCase($input)
    {
        return (!$input ? strtolower(str_replace(' ', '_', $input)) : '');
    }
}
