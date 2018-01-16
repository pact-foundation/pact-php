<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Model\Scripts;

/**
 * Interface BinaryDownloaderInterface
 */
interface InstallerInterface
{
    /**
     * Verify if the downloader works for the current environment.
     *
     * @return bool
     */
    public function isEligible(): bool;

    /**
     * Download the file and install it in the necessary directory.
     *
     * @param string $destinationDir folder path to put the binaries
     *
     * @return Scripts
     */
    public function install(string $destinationDir): Scripts;
}
