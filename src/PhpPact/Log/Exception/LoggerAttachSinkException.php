<?php

namespace PhpPact\Log\Exception;

class LoggerAttachSinkException extends LoggerException
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            -1 => "Can't set logger (applying the logger failed, perhaps because one is applied already).",
            -2 => 'No logger has been initialized (call `pactffi_logger_init` before any other log function).',
            -3 => 'The sink specifier was not UTF-8 encoded.',
            -4 => 'The sink type specified is not a known type (known types: "stdout", "stderr", "buffer", or "file /some/path").',
            -5 => 'No file path was specified in a file-type sink specification.',
            -6 => 'Opening a sink to the specified file path failed (check permissions).',
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
