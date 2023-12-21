<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface MatchingRulesStorageInterface
{
    public const REQUEST_DOMAIN = 'request';
    public const RESPONSE_DOMAIN = 'response';

    public function add(string $domain, int $id, string $file): void;

    public function get(string $domain, int $id): ?string;
}
