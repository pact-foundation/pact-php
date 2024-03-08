<?php

namespace PhpPactTest\FFI;

use FFI;
use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private ClientInterface $client;

    public function setUp(): void
    {
        $this->client = new Client();
    }

    public function testGet(): void
    {
        $this->assertSame(5, $this->client->get('LevelFilter_Trace'));
    }

    public function testCall(): void
    {
        $this->expectNotToPerformAssertions();
        $this->client->call('pactffi_string_delete', null);
    }
}
