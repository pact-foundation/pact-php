<?php

namespace PhpPact\Consumer\Matcher;

/**
 * @deprecated Use PhpPact\Consumer\Matcher\Enum\HttpStatus instead
 */
class HttpStatus
{
    public const INFORMATION = 'info';
    public const SUCCESS = 'success';
    public const REDIRECT = 'redirect';
    public const CLIENT_ERROR = 'clientError';
    public const SERVER_ERROR = 'serverError';
    public const NON_ERROR = 'nonError';
    public const ERROR = 'error';
}
