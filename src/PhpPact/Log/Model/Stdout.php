<?php

namespace PhpPact\Log\Model;

class Stdout extends AbstractSink
{
    public function getSpecifier(): string
    {
        return 'stdout';
    }
}
