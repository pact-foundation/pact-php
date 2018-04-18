<?php

namespace PhpPact\Standalone\MockService\Service;

use PhpPact\Consumer\Model\Interaction;
use PhpPact\Exception\ConnectionException;

/**
 * Interface MockServerHttpServiceInterface
 */
interface MockServerHttpServiceInterface
{
    /**
     * Verify that the Ruby PhpPact Mock Server is running.
     *
     * @throws ConnectionException
     *
     * @return bool
     */
    public function healthCheck(): bool;

    /**
     * Delete all interactions.
     *
     * @return bool
     */
    public function deleteAllInteractions(): bool;

    /**
     * Create a single interaction.
     *
     * @param Interaction $interaction
     *
     * @return bool
     */
    public function registerInteraction(Interaction $interaction): bool;

    /**
     * Verify that all interactions have taken place.
     *
     * @return bool
     */
    public function verifyInteractions(): bool;

    /**
     * Get the current state of the PACT JSON file and write it to disk.
     *
     * @return string
     */
    public function getPactJson(): string;
}
