<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptions;
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

        $subject = new VerifierConfig();
        $subject->getProviderInfo()
            ->setName($providerName)
            ->setScheme($scheme)
            ->setHost($host)
            ->setPort($port)
            ->setPath($basePath);
        $subject->getFilterInfo()
            ->setFilterDescription($filterDescription)
            ->setFilterNoState($filterNoState)
            ->setFilterState($filterState);
        $subject->getProviderState()
            ->setStateChangeUrl($stateChangeUrl)
            ->setStateChangeAsBody($stateChangeAsBody)
            ->setStateChangeTeardown($stateChangeTeardown);
        $subject->getVerificationOptions()
            ->setRequestTimeout($requestTimeout)
            ->setDisableSslVerification($disableSslVerification);
        $publishOptions = new PublishOptions();
        $publishOptions
            ->setProviderTags($providerTags)
            ->setProviderVersion($providerVersion)
            ->setBuildUrl($buildUrl)
            ->setProviderBranch($providerBranch);
        $subject->setPublishOptions($publishOptions);
        $subject->getConsumerFilters()
            ->setFilterConsumerNames($filterConsumerNames);

        $providerInfo = $subject->getProviderInfo();
        static::assertSame($providerName, $providerInfo->getName());
        static::assertSame($scheme, $providerInfo->getScheme());
        static::assertSame($host, $providerInfo->getHost());
        static::assertSame($port, $providerInfo->getPort());
        static::assertSame($basePath, $providerInfo->getPath());
        $filterInfo = $subject->getFilterInfo();
        static::assertSame($filterDescription, $filterInfo->getFilterDescription());
        static::assertSame($filterNoState, $filterInfo->getFilterNoState());
        static::assertSame($filterState, $filterInfo->getFilterState());
        $providerState = $subject->getProviderState();
        static::assertSame($stateChangeUrl, $providerState->getStateChangeUrl());
        static::assertSame($stateChangeAsBody, $providerState->isStateChangeAsBody());
        static::assertSame($stateChangeTeardown, $providerState->isStateChangeTeardown());
        $verificationOptions = $subject->getVerificationOptions();
        static::assertSame($requestTimeout, $verificationOptions->getRequestTimeout());
        static::assertSame($disableSslVerification, $verificationOptions->isDisableSslVerification());
        static::assertSame($publishResults, $subject->isPublishResults());
        $publishOptions = $subject->getPublishOptions();
        static::assertSame($providerTags, $publishOptions->getProviderTags());
        static::assertSame($providerVersion, $publishOptions->getProviderVersion());
        static::assertSame($buildUrl, $publishOptions->getBuildUrl());
        static::assertSame($providerBranch, $publishOptions->getProviderBranch());
        $consumerFilters = $subject->getConsumerFilters();
        static::assertSame($filterConsumerNames, $consumerFilters->getFilterConsumerNames());
    }
}
