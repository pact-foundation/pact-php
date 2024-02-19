<?php

namespace PhpPact\Plugin\Enum;

enum InteractionPartUsingPlugin
{
    case REQUEST;
    case RESPONSE;
    case BOTH;

    public function isRequest(): bool
    {
        return match($this) {
            self::REQUEST => true,
            self::RESPONSE => false,
            self::BOTH => true,
        };
    }

    public function isResponse(): bool
    {
        return match($this) {
            self::REQUEST => false,
            self::RESPONSE => true,
            self::BOTH => true,
        };
    }
}
