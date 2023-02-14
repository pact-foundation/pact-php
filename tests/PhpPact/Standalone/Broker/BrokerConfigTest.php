<?php

namespace PhpPactTest\Standalone\Broker;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\Broker\BrokerConfig;
use PHPUnit\Framework\TestCase;

class BrokerConfigTest extends TestCase
{
    public function testSetters()
    {
        $brokerUri      = new Uri('http://localhost');
        $brokerToken    = 'abc-123';
        $brokerUsername = 'user';
        $brokerPassword = 'pass';

        $verbose     = true;
        $pacticipant = 'a pacticipant';

        $request     = 'POST';
        $header      = 'Accept application/json';
        $data        = '{"key": "value"}';
        $user        = 'username:password';
        $url         = 'https://example.org/webhook';
        $consumer    = 'test-consumer';
        $provider    = 'test-provider';
        $description = 'an example webhook';
        $uuid        = 'd2181b32-8b03-4daf-8cc0-d9168b2f6fac';

        $version = '1.2.3';
        $branch  = 'new-feature';
        $tag     = 'prod';

        $name          = 'My Project';
        $repositoryUrl = 'https://github.com/vendor/my-project';

        $consumerVersion = '1.1.2';
        $pactLocations   = '/path/to/pacts';

        $subject = (new BrokerConfig())
            ->setBrokerUri($brokerUri)
            ->setBrokerToken($brokerToken)
            ->setBrokerUsername($brokerUsername)
            ->setBrokerPassword($brokerPassword)
            ->setVerbose($verbose)
            ->setPacticipant($pacticipant)
            ->setRequest($request)
            ->setHeader($header)
            ->setData($data)
            ->setUser($user)
            ->setUrl($url)
            ->setConsumer($consumer)
            ->setProvider($provider)
            ->setDescription($description)
            ->setUuid($uuid)
            ->setVersion($version)
            ->setBranch($branch)
            ->setTag($tag)
            ->setName($name)
            ->setRepositoryUrl($repositoryUrl)
            ->setConsumerVersion($consumerVersion)
            ->setPactLocations($pactLocations);

        static::assertSame($brokerUri, $subject->getBrokerUri());
        static::assertSame($brokerToken, $subject->getBrokerToken());
        static::assertSame($brokerUsername, $subject->getBrokerUsername());
        static::assertSame($brokerPassword, $subject->getBrokerPassword());
        static::assertSame($verbose, $subject->isVerbose());
        static::assertSame($pacticipant, $subject->getPacticipant());
        static::assertSame($request, $subject->getRequest());
        static::assertSame($header, $subject->getHeader());
        static::assertSame($data, $subject->getData());
        static::assertSame($user, $subject->getUser());
        static::assertSame($url, $subject->getUrl());
        static::assertSame($consumer, $subject->getConsumer());
        static::assertSame($provider, $subject->getProvider());
        static::assertSame($description, $subject->getDescription());
        static::assertSame($uuid, $subject->getUuid());
        static::assertSame($version, $subject->getVersion());
        static::assertSame($branch, $subject->getBranch());
        static::assertSame($tag, $subject->getTag());
        static::assertSame($name, $subject->getName());
        static::assertSame($repositoryUrl, $subject->getRepositoryUrl());
        static::assertSame($consumerVersion, $subject->getConsumerVersion());
        static::assertSame($pactLocations, $subject->getPactLocations());
    }
}
