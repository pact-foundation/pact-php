<?php

namespace PhpPact\Log\Enum;

enum LogLevel: string
{
    case TRACE = 'TRACE';
    case DEBUG = 'DEBUG';
    case INFO = 'INFO';
    case WARN = 'WARN';
    case ERROR = 'ERROR';
    case OFF = 'OFF';
    case NONE = 'NONE';
}
