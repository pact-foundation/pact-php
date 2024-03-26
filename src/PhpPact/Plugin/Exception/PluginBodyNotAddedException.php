<?php

namespace PhpPact\Plugin\Exception;

class PluginBodyNotAddedException extends PluginException
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            1 => 'A general panic was caught.',
            2 => 'The mock server has already been started.',
            3 => 'The interaction handle is invalid.',
            4 => 'The content type is not valid.',
            5 => 'The contents JSON is not valid JSON.',
            6 => 'The plugin returned an error.',
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
