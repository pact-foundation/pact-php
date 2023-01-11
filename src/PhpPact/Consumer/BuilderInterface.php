<?php

namespace PhpPact\Consumer;

/**
 * Build an Pact and send it to the Ruby Standalone Mock Service
 * Class BuilderInterface.
 */
interface BuilderInterface
{
    /**
     * Verify that the interactions are valid.
     */
    public function verify(): bool;
}
