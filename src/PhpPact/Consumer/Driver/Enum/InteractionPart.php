<?php

namespace PhpPact\Consumer\Driver\Enum;

enum InteractionPart
{
    case REQUEST;
    case RESPONSE;

    public function isRequest(): bool
    {
        return $this === self::REQUEST;
    }

    public function isResponse(): bool
    {
        return $this === self::RESPONSE;
    }
}
