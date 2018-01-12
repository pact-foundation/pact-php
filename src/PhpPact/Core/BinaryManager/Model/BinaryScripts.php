<?php

namespace PhpPact\Core\BinaryManager\Model;

/**
 * Represents locations of Ruby Standalone full path and scripts.
 * Class BinaryScripts
 */
class BinaryScripts
{
    /**
     * Path to PhpPact Mock Service
     *
     * @var string
     */
    private $mockService;

    public function __construct(string $mockService)
    {
        $this->mockService = $mockService;
    }

    /**
     * @return string
     */
    public function getMockService(): string
    {
        return $this->mockService;
    }
}
