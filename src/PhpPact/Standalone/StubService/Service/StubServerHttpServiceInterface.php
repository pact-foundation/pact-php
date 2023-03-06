<?php

namespace PhpPact\Standalone\StubService\Service;

/**
 * Interface StubServerHttpServiceInterface.
 */
interface StubServerHttpServiceInterface
{
    /**
     * Get the current state of the PACT JSON file and write it to disk.
     *
     * @return string
     */
    public function getJson(): string;
}
