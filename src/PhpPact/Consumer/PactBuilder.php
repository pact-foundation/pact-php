<?php

namespace PhpPact\Consumer;

use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;

/**
 * Build an Pact and send it to the Ruby Standalone Mock Service
 * Class PactBuilder.
 */
class PactBuilder
{
    /** @var MockServerConfigInterface */
    protected $config;

    /** @var MockServerHttpService */
    protected $mockServerHttpService;

    /**
     * PactBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->mockServerHttpService = new MockServerHttpService(new GuzzleClient(), $config);
        $this->config                = $config;
    }

    /**
     * Verify that the interactions are valid.
     */
    public function verify(): bool
    {
        return $this->mockServerHttpService->verifyInteractions();
    }

    /**
     * Writes the file to disk and deletes interactions from mock server.
     */
    public function finalize(): bool
    {
        // Write the pact file to disk.
        $this->mockServerHttpService->getPactJson();

        // Delete the interactions.
        $this->mockServerHttpService->deleteAllInteractions();

        return true;
    }
    /**
     * Write the Pact without deleting the interactions.
     *
     * @return bool
     */
    public function writePact(): bool
    {
        // Write the pact file to disk.
        $this->mockServerHttpService->getPactJson();

        return true;
    }
}
