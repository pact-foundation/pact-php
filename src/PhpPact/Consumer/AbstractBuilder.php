<?php

namespace PhpPact\Consumer;

use FFI;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Class AbstractBuilder.
 */
abstract class AbstractBuilder implements BuilderInterface
{
    public const SUPPORTED_PACT_SPECIFICATION_VERSIONS = [
        '1.0.0' => 1,
        '1.1.0' => 2,
        '2.0.0' => 3,
        '3.0.0' => 4,
        '4.0.0' => 5,
    ];
    public const UNKNOWN_PACT_SPECIFICATION_VERSION = 0;

    protected FFI $ffi;
    protected MockServerConfigInterface $config;
    protected Scripts $scripts;

    /**
     * @param MockServerConfigInterface $config
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->config  = $config;
        $this->scripts = (new InstallManager())->install();
        $this->ffi     = FFI::cdef(\file_get_contents($this->scripts->getCode()), $this->scripts->getLibrary());
        $this->ffi->pactffi_init('PACT_LOGLEVEL');
    }

    /**
     * @return int
     */
    protected function getPactSpecificationVersion(): int
    {
        return static::SUPPORTED_PACT_SPECIFICATION_VERSIONS[$this->config->getPactSpecificationVersion()] ?? static::UNKNOWN_PACT_SPECIFICATION_VERSION;
    }
}
