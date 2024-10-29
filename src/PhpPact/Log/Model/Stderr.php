<?php

namespace PhpPact\Log\Model;

class Stderr extends AbstractSink
{
    public function getSpecifier(): string
    {
        return 'stderr';
    }
}
