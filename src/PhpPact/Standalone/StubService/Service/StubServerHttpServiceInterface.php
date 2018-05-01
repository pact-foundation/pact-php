<?php

namespace PhpPact\Standalone\StubService\Service;


use PhpPact\Exception\ConnectionException;

/**
 * Interface StubServerHttpServiceInterface
 */
interface StubServerHttpServiceInterface
{
    /**
     * Verify that the Ruby PhpPact Stub Server is running.
     *
     * @throws ConnectionException
     *
     * @return bool
     */
    public function healthCheck(): bool;

    /**
     * Get the current state of the PACT JSON file and write it to disk.
     *
     * @return string
     */
    public function getJson(): string;
}
