<?php

namespace PhpPact\Standalone\Installer\Exception;

use Exception;

/**
 * File failed to download from external source.
 * Class FileDownloadFailureException
 */
class FileDownloadFailureException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
