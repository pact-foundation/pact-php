<?php

namespace PhpPact\Standalone\Installer\Exception;

use Exception;

/**
 * Unable to find a downloader to get the binaries.
 * Class NoDownloaderFoundException.
 */
class NoDownloaderFoundException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
