<?php

namespace PhpPact\Log\Model;

class Buffer extends AbstractSink
{
    public function getSpecifier(): string
    {
        return 'buffer';
    }
}
