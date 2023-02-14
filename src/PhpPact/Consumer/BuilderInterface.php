<?php

namespace PhpPact\Consumer;

/**
 * Build an Pact and send it to the Ruby Standalone Mock Service
 */
interface BuilderInterface
{
    /**
     * Verify that the interactions are valid.
     */
    public function verify(): bool;

    /**
     * Write the Pact without deleting the interactions.
     *
     * @return bool
     */
    public function writePact(): bool;
}
