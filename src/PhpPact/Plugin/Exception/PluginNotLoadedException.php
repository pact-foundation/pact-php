<?php

namespace PhpPact\Plugin\Exception;

class PluginNotLoadedException extends PluginException
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            1 => 'A general panic was caught.',
            2 => 'Failed to load the plugin.',
            3 => 'Pact Handle is not valid.',
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
