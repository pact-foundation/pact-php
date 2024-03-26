<?php

namespace PhpPact\Consumer\Matcher;

class HttpStatus
{
    public const INFORMATION = 'info';
    public const SUCCESS = 'success';
    public const REDIRECT = 'redirect';
    public const CLIENT_ERROR = 'clientError';
    public const SERVER_ERROR = 'serverError';
    public const NON_ERROR = 'nonError';
    public const ERROR = 'error';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::INFORMATION,
            self::SUCCESS,
            self::REDIRECT,
            self::CLIENT_ERROR,
            self::SERVER_ERROR,
            self::NON_ERROR,
            self::ERROR,
        ];
    }
}
