<?php

namespace PhpPact\Plugin\Exception;

class PluginNotSupportedBySpecificationException extends PluginException
{
    public function __construct(string $specification)
    {
        parent::__construct(sprintf(
            'Plugin is not supported by specification %s, use 4.0.0 or above',
            $specification,
        ));
    }
}
