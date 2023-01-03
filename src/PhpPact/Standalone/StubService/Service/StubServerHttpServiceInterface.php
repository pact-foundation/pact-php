<?php

namespace PhpPact\Standalone\StubService\Service;

interface StubServerHttpServiceInterface
{
    /**
     * Get the current state of the PACT JSON file and write it to disk.
     */
    public function getJson(): string;
}
