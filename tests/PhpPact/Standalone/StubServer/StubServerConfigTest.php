<?php

namespace PhpPactTest\Standalone\StubServer;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\StubService\StubServerConfig;
use PHPUnit\Framework\TestCase;

class StubServerConfigTest extends TestCase
{
    public function testSetters(): void
    {
        $brokerUrl               = new Uri('http://localhost');
        $port                    = 1234;
        $extension               = 'json';
        $logLevel                = 'debug';
        $providerState           = 'state';
        $providerStateHeaderName = 'header';
        $token                   = 'token';
        $user                    = 'user:password';
        $dirs                    = ['/path/to/pacts'];
        $files                   = ['/path/to/pact.json'];
        $urls                    = ['http://example.com/path/to/file.json'];
        $consumerNames           = ['consumer-1', 'consumer-2'];
        $providerNames           = ['provider-1', 'provider-2'];
        $cors                    = true;
        $corsReferer             = true;
        $emptyProviderState      = true;
        $insecureTls             = true;

        $subject = (new StubServerConfig())
            ->setBrokerUrl($brokerUrl)
            ->setPort($port)
            ->setExtension($extension)
            ->setLogLevel($logLevel)
            ->setProviderState($providerState)
            ->setProviderStateHeaderName($providerStateHeaderName)
            ->setToken($token)
            ->setUser($user)
            ->setDirs($dirs)
            ->setFiles($files)
            ->setUrls($urls)
            ->setConsumerNames($consumerNames)
            ->setProviderNames($providerNames)
            ->setCors($cors)
            ->setCorsReferer($corsReferer)
            ->setEmptyProviderState($emptyProviderState)
            ->setInsecureTls($insecureTls);

        static::assertSame($brokerUrl, $subject->getBrokerUrl());
        static::assertSame($port, $subject->getPort());
        static::assertSame($extension, $subject->getExtension());
        static::assertSame($logLevel, $subject->getLogLevel());
        static::assertSame($providerState, $subject->getProviderState());
        static::assertSame($providerStateHeaderName, $subject->getProviderStateHeaderName());
        static::assertSame($token, $subject->getToken());
        static::assertSame($user, $subject->getUser());
        static::assertSame($dirs, $subject->getDirs());
        static::assertSame($files, $subject->getFiles());
        static::assertSame($urls, $subject->getUrls());
        static::assertSame($consumerNames, $subject->getConsumerNames());
        static::assertSame($providerNames, $subject->getProviderNames());
        static::assertSame($cors, $subject->isCors());
        static::assertSame($corsReferer, $subject->isCorsReferer());
        static::assertSame($emptyProviderState, $subject->isEmptyProviderState());
        static::assertSame($insecureTls, $subject->isInsecureTls());
    }
}
