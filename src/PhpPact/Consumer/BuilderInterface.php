<?php




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

    /**
     * Writes the file to disk and deletes interactions from mock server.
     */
    public function finalize(): bool;

    /**
     * Write the Pact without deleting the interactions.
     *
     * @return bool
     */
    public function writePact(): bool;

}
