<?php

namespace PhpPact\Consumer\Exception;

class MockServerPactFileNotWrittenException extends ConsumerException
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            1 => 'A general panic was caught',
            2 => 'The pact file was not able to be written',
            3 => 'A mock server with the provided port was not found',
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
