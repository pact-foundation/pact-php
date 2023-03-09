<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

class VerifierConfigTest extends TestCase
{
    public function testSetters()
    {
        $providerName           = 'someProvider';
        $scheme                 = 'https';
        $host                   = 'test-host';
        $port                   = 7202;
        $basePath               = '/path';
        $filterDescription      = 'request to /hello';
        $filterNoState          = true;
        $filterState            = 'given state';
        $stateChangeUrl         = new Uri('http://domain.com/change');
        $stateChangeAsBody      = true;
        $stateChangeTeardown    = true;
        $requestTimeout         = 500;
        $disableSslVerification = true;
        $publishResults         = true;
        $providerTags           = ['feature-x', 'master', 'test', 'prod'];
        $providerVersion        = '1.2.3';
        $buildUrl               = new Uri('http://ci/build/1');
        $providerBranch         = 'some-branch';
        $filterConsumerNames    = ['http-consumer-1', 'http-consumer-2', 'message-consumer-2'];

        $subject = (new VerifierConfig())
            // Provider Info
            ->setProviderName($providerName)
            ->setScheme($scheme)
            ->setHost($host)
            ->setPort($port)
            ->setBasePath($basePath)
            // Filter Info
            ->setFilterDescription($filterDescription)
            ->setFilterNoState($filterNoState)
            ->setFilterState($filterState)
            // Provider State
            ->setStateChangeUrl($stateChangeUrl)
            ->setStateChangeAsBody($stateChangeAsBody)
            ->setStateChangeTeardown($stateChangeTeardown)
            // Verification Options
            ->setRequestTimeout($requestTimeout)
            ->setDisableSslVerification($disableSslVerification)
            // Publish Options
            ->setPublishResults($publishResults)
            ->setProviderTags(...$providerTags)
            ->setProviderVersion($providerVersion)
            ->setBuildUrl($buildUrl)
            ->setProviderBranch($providerBranch)
            // Consumer Filters
            ->setFilterConsumerNames(...$filterConsumerNames);

        static::assertSame($providerName, $subject->getProviderName());
        static::assertSame($scheme, $subject->getScheme());
        static::assertSame($host, $subject->getHost());
        static::assertSame($port, $subject->getPort());
        static::assertSame($basePath, $subject->getBasePath());
        static::assertSame($filterDescription, $subject->getFilterDescription());
        static::assertSame($filterNoState, $subject->getFilterNoState());
        static::assertSame($filterState, $subject->getFilterState());
        static::assertSame($stateChangeUrl, $subject->getStateChangeUrl());
        static::assertSame($stateChangeAsBody, $subject->isStateChangeAsBody());
        static::assertSame($stateChangeTeardown, $subject->isStateChangeTeardown());
        static::assertSame($requestTimeout, $subject->getRequestTimeout());
        static::assertSame($disableSslVerification, $subject->isDisableSslVerification());
        static::assertSame($publishResults, $subject->isPublishResults());
        static::assertSame($providerTags, $subject->getProviderTags());
        static::assertSame($providerVersion, $subject->getProviderVersion());
        static::assertSame($buildUrl, $subject->getBuildUrl());
        static::assertSame($providerBranch, $subject->getProviderBranch());
        static::assertSame($filterConsumerNames, $subject->getFilterConsumerNames());
    }
}
