<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Interaction;

interface InteractionsStorageInterface
{
    public const SERVER_DOMAIN = 'server';
    public const CLIENT_DOMAIN = 'client';
    public const PACT_WRITER_DOMAIN = 'pact-writer';

    public function add(string $domain, int $id, Interaction $interaction, bool $clone = false): void;

    public function get(string $domain, int $id): Interaction;
}
