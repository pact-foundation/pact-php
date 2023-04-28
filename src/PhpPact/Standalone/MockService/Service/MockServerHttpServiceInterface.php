<?php

namespace PhpPact\Standalone\MockService\Service;

use PhpPact\Consumer\Model\Interaction;
use PhpPact\Exception\ConnectionException;

interface MockServerHttpServiceInterface
{
    /**
     * Verify that the Ruby PhpPact Mock Server is running.
     *
     * @throws ConnectionException
     */
    public function healthCheck(): bool;

    /**
     * Delete all interactions.
     */
    public function deleteAllInteractions(): bool;

    /**
     * Create a single interaction.
     */
    public function registerInteraction(Interaction $interaction): bool;

    /**
     * Verify that all interactions have taken place.
     */
    public function verifyInteractions(): bool;

    /**
     * Get the current state of the PACT JSON file and write it to disk.
     */
    public function getPactJson(): string;
}
