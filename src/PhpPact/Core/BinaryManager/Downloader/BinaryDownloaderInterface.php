<?php

namespace PhpPact\Core\BinaryManager\Downloader;

use PhpPact\Core\BinaryManager\Model\BinaryScripts;

/**
 * Interface BinaryDownloaderInterface
 */
interface BinaryDownloaderInterface
{
    /**
     * Verify if the downloader works for the current environment.
     *
     * @return bool
     */
    public function checkEligibility(): bool;

    /**
     * Download the file and install it in the necessary directory.
     *
     * @param string $destinationDir folder path to put the binaries
     *
     * @return BinaryScripts
     */
    public function install(string $destinationDir): BinaryScripts;
}
