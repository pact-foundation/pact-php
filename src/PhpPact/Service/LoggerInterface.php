<?php

namespace PhpPact\Service;

interface LoggerInterface
{
    public function log(string $output): void;
}
