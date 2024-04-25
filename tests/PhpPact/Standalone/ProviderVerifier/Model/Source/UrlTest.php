<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model\Source;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testSetters(): void
    {
        $url      = new Uri('http://example.com/path/to/pact.json');
        $token    = 'test-123';
        $username = 'test';
        $password = '111';

        $subject = (new Url())
            ->setUrl($url)
            ->setToken($token)
            ->setUsername($username)
            ->setPassword($password);

        static::assertSame($url, $subject->getUrl());
        static::assertSame($token, $subject->getToken());
        static::assertSame($username, $subject->getUsername());
        static::assertSame($password, $subject->getPassword());
    }
}
