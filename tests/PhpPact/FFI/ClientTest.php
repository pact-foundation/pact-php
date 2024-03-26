<?php

namespace PhpPactTest\FFI;

use FFI;
use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\Attributes\TestWith;
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

    #[TestWith(['abc123', true])]
    #[TestWith(['testing', false])]
    public function testCall(string $example, bool $result): void
    {
        $this->assertSame($result, $this->client->call('pactffi_check_regex', '\w{3}\d+', $example));
    }
}
