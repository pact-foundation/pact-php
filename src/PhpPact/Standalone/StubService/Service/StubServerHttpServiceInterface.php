<?php

namespace PhpPact\Standalone\StubService\Service;

use PhpPact\Exception\ConnectionException;

interface StubServerHttpServiceInterface
{
    /**
     * Verify that the Ruby PhpPact Stub Server is running.
     *
     * @throws ConnectionException
     */
    public function healthCheck(): bool;

    /**
     * Get the current state of the PACT JSON file and write it to disk.
     */
    public function getJson(): string;
}
