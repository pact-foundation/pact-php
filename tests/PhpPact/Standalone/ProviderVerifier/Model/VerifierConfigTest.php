<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\Config\CallingApp;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ConsumerFilters;
use PhpPact\Standalone\ProviderVerifier\Model\Config\CustomHeaders;
use PhpPact\Standalone\ProviderVerifier\Model\Config\FilterInfo;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderInfo;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderState;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptions;
use PhpPact\Standalone\ProviderVerifier\Model\Config\VerificationOptions;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

class VerifierConfigTest extends TestCase
{
    public function testSetters(): void
    {
        $callingApp = new CallingApp();
        $providerInfo = new ProviderInfo();
        $filterInfo = new FilterInfo();
        $providerState = new ProviderState();
        $verificationOptions = new VerificationOptions();
        $publishOptions = new PublishOptions();
        $consumerFilters = new ConsumerFilters();
        $providerTransports = [
            new ProviderTransport(),
            new ProviderTransport(),
            new ProviderTransport(),
        ];
        $customHeaders = new CustomHeaders();

        $subject = new VerifierConfig();
        $subject->setCallingApp($callingApp);
        $subject->setProviderInfo($providerInfo);
        $subject->setFilterInfo($filterInfo);
        $subject->setProviderState($providerState);
        $subject->setVerificationOptions($verificationOptions);
        $subject->setPublishOptions($publishOptions);
        $subject->setConsumerFilters($consumerFilters);
        $subject->setProviderTransports($providerTransports);
        $subject->setCustomHeaders($customHeaders);

        $this->assertSame($callingApp, $subject->getCallingApp());
        $this->assertSame($providerInfo, $subject->getProviderInfo());
        $this->assertSame($filterInfo, $subject->getFilterInfo());
        $this->assertSame($providerState, $subject->getProviderState());
        $this->assertSame($verificationOptions, $subject->getVerificationOptions());
        $this->assertTrue($subject->isPublishResults());
        $this->assertSame($publishOptions, $subject->getPublishOptions());
        $this->assertSame($consumerFilters, $subject->getConsumerFilters());
        $this->assertSame($providerTransports, $subject->getProviderTransports());
        $this->assertSame($customHeaders, $subject->getCustomHeaders());
    }
}
