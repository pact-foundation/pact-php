<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Interaction;

interface InteractionsStorageInterface
{
    public const MOCK_SERVER_DOMAIN = 'mock-server';
    public const MOCK_SERVER_CLIENT_DOMAIN = 'mock-server-client';
    public const PROVIDER_DOMAIN = 'provider';
    public const PACT_WRITER_DOMAIN = 'pact-writer';

    public function add(string $domain, int $id, Interaction $interaction, bool $clone = false): void;

    public function get(string $domain, int $id): Interaction;
}
