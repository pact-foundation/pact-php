<?php

namespace PhpPact\Standalone\Installer\Model;

/**
 * Class Scripts.
 */
class Scripts
{
    /**
     * Path to Pact FFI C Header Code.
     */
    private string $code;

    /**
     * Path to Pact FFI Dynamic Library.
     */
    private string $library;

    /**
     * Path to the PhpPact Stub Service.
     *
     * @var string
     */
    private string $stubService;

    /**
     * Path to the Ruby Standalone Broker.
     *
     * @var string
     */
    private string $broker;

    public function __construct(string $code, string $library, string $stubService, string $broker)
    {
        $this->code              = $code;
        $this->library           = $library;
        $this->stubService       = $stubService;
        $this->broker            = $broker;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getLibrary(): string
    {
        return $this->library;
    }

    /**
     * @return string
     */
    public function getStubService(): string
    {
        return $this->stubService;
    }

    /**
     * @return string
     */
    public function getBroker(): string
    {
        return $this->broker;
    }
}
