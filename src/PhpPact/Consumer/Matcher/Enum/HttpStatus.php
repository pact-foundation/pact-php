<?php

namespace PhpPact\Consumer\Matcher\Enum;

use PhpPact\Consumer\Matcher\Model\Range;

enum HttpStatus: string
{
    case INFORMATION = 'info';
    case SUCCESS = 'success';
    case REDIRECT = 'redirect';
    case CLIENT_ERROR = 'clientError';
    case SERVER_ERROR = 'serverError';
    case NON_ERROR = 'nonError';
    case ERROR = 'error';

    public function range(): Range
    {
        return match($this) {
            self::INFORMATION => new Range(100, 199),
            self::SUCCESS => new Range(200, 299),
            self::REDIRECT => new Range(300, 399),
            self::CLIENT_ERROR => new Range(400, 499),
            self::SERVER_ERROR => new Range(500, 599),
            self::NON_ERROR => new Range(100, 399),
            self::ERROR => new Range(400, 599),
        };
    }
}
