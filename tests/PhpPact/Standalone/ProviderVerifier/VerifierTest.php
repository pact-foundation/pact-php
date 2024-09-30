<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use FFI;
use FFI\CData;
use GuzzleHttp\Psr7\Uri;
use PhpPact\FFI\ClientInterface;
use PhpPact\Service\LoggerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptions;
use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\Selector\Selector;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Url;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    use ClientTrait;

    private Verifier $verifier;
    private VerifierConfigInterface $config;
    private LoggerInterface&MockObject $logger;
    private CData $handle;

    protected function setUp(): void
    {
        $this->config = new VerifierConfig();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->client = $this->createMock(ClientInterface::class);
        $handle = FFI::new('int');
        $this->assertInstanceOf(CData::class, $handle);
        $this->handle = $handle;
    }

    private function setUpCalls(bool $hasProviderTags = true, bool $hasFilterConsumerNames = true): void
    {
        $this->config->getCallingApp()
            ->setName($callingAppName = 'calling app name')
            ->setVersion($callingAppVersion = '1.2.3');
        $this->config->getProviderInfo()
            ->setName($providerName = 'provider name')
            ->setScheme($providerScheme = 'https')
            ->setHost($providerHost = 'provider.domain')
            ->setPort($providerPort = 123)
            ->setPath($providerPath = '/provider/path');
        $transport = new ProviderTransport();
        $transport->setProtocol($transportProtocol = 'message')
            ->setPort($transportPort = 234)
            ->setPath($transportPath = '/provider-messages')
            ->setScheme($transportScheme = 'http');
        $this->config->getProviderState()
            ->setStateChangeUrl($stateChangeUrl = new Uri('http://provider.host:432/pact-change-state'))
            ->setStateChangeTeardown($stateChangeTearDown = true)
            ->setStateChangeAsBody($stateChangeAsBody = true);
        $this->config->addProviderTransport($transport);
        $this->config->getFilterInfo()
            ->setFilterDescription($filterDescription = 'request to /hello')
            ->setFilterNoState($filterNoState = true)
            ->setFilterState($filterState = 'given state');
        $this->config->getVerificationOptions()
            ->setRequestTimeout($requestTimeout = 500)
            ->setDisableSslVerification($disableSslVerification = true);
        $publishOptions = new PublishOptions();
        $publishOptions
            ->setProviderTags($providerTags = $hasProviderTags ? ['feature-x', 'master', 'test', 'prod'] : [])
            ->setProviderVersion($providerVersion = '1.2.3')
            ->setBuildUrl($buildUrl = new Uri('http://ci/build/1'))
            ->setProviderBranch($providerBranch = 'some-branch');
        $this->config->setPublishOptions($publishOptions);
        $this->config->getConsumerFilters()
            ->setFilterConsumerNames($filterConsumerNames = $hasFilterConsumerNames ? ['http-consumer-1', 'http-consumer-2', 'message-consumer-2'] : []);
        $this->config->getCustomHeaders()
            ->setHeaders($customHeaders = ['name-1' => 'value-1', 'name-2' => 'value-2']);
        $this->config->setLogLevel($logLevel = 'info');
        $this->expectsVerifierNewForApplication($callingAppName, $callingAppVersion, $this->handle);
        $this->expectsVerifierSetProviderInfo($this->handle, $providerName, $providerScheme, $providerHost, $providerPort, $providerPath);
        $this->expectsVerifierAddProviderTransport($this->handle, $transportProtocol, $transportPort, $transportPath, $transportScheme);
        $this->expectsVerifierSetFilterInfo($this->handle, $filterDescription, $filterState, $filterNoState);
        $this->expectsVerifierSetProviderState($this->handle, (string) $stateChangeUrl, $stateChangeTearDown, $stateChangeAsBody);
        $this->expectsVerifierSetVerificationOptions($this->handle, $disableSslVerification, $requestTimeout, 0);
        $this->expectsVerifierSetPublishOptions(
            $this->handle,
            $providerVersion,
            $buildUrl,
            $providerTags,
            $providerBranch,
            0
        );
        $this->expectsVerifierSetConsumerFilters($this->handle, $filterConsumerNames);
        $this->expectsVerifierAddCustomHeader($this->handle, $customHeaders);
        $this->expectsInitWithLogLevel(strtoupper($logLevel));
    }

    #[TestWith([true,  true])]
    #[TestWith([false, false])]
    #[TestWith([true,  false])]
    #[TestWith([false, true])]
    public function testConstruct(bool $hasProviderTags, bool $hasFilterConsumerNames): void
    {
        $this->setUpCalls($hasProviderTags, $hasFilterConsumerNames);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
    }

    public function testAddFile(): void
    {
        $this->setUpCalls();
        $file = '/path/to/file.json';
        $this->expectsVerifierAddFileSource($this->handle, $file);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addFile($file));
    }

    public function testAddDirectory(): void
    {
        $this->setUpCalls();
        $directory = '/path/to/directory';
        $this->expectsVerifierAddDirectorySource($this->handle, $directory);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addDirectory($directory));
    }

    public function testAddUrl(): void
    {
        $this->setUpCalls();
        $source = new Url();
        $source
            ->setUrl($url = new Uri('http://example.test/path/to/file.json'))
            ->setToken($token = 'secret token')
            ->setUsername($username = 'my username')
            ->setPassword($password = 'secret password');
        $this->expectsVerifierAddUrlSource($this->handle, (string) $url, $username, $password, $token);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addUrl($source));
    }

    #[TestWith([true,  true,  true])]
    #[TestWith([false, false, false])]
    #[TestWith([true,  false, false])]
    #[TestWith([false, true,  false])]
    #[TestWith([false, false, true])]
    public function testAddBroker(bool $hasVersionSelectors, bool $hasProviderTags, bool $hasConsumerVersionTags): void
    {
        $this->setUpCalls();
        $consumerVersionSelectors = (new ConsumerVersionSelectors());
        if ($hasVersionSelectors) {
            $consumerVersionSelectors
                ->addSelector(new Selector(tag: 'foo', latest: true))
                ->addSelector('{"tag":"bar","latest":true}');
        }
        $source = new Broker();
        $source
            ->setUrl($url = new Uri('http://example.test/path/to/file.json'))
            ->setToken($token = 'secret token')
            ->setUsername($username = 'my username')
            ->setPassword($password = 'secret password')
            ->setEnablePending($enablePending = true)
            ->setIncludeWipPactSince($includeWipPactSince = '2020-01-30')
            ->setProviderTags($providerTags = $hasProviderTags ? ['prod', 'staging'] : [])
            ->setProviderBranch($providerBranch = 'main')
            ->setConsumerVersionSelectors($consumerVersionSelectors)
            ->setConsumerVersionTags($consumerVersionTags = $hasConsumerVersionTags ? ['dev'] : []);
        $this->expectsVerifierBrokerSourceWithSelectors(
            $this->handle,
            (string) $url,
            $username,
            $password,
            $token,
            $enablePending,
            $includeWipPactSince,
            $providerTags,
            $providerBranch,
            $consumerVersionSelectors,
            $consumerVersionTags,
        );
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addBroker($source));
    }

    #[TestWith([0, true,  false, null])]
    #[TestWith([0, true,  true,  null])]
    #[TestWith([0, true,  true,  '{"key": "value"}'])]
    #[TestWith([1, false, false, null])]
    #[TestWith([2, false, false, null])]
    public function testVerify(int $error, bool $success, bool $hasLogger, ?string $json): void
    {
        $this->setUpCalls();
        $json = '{"key": "value"}';
        $this->expectsVerifierExecute($this->handle, $error);
        $this->expectsVerifierJson($this->handle, $hasLogger, $json);
        $this->logger
            ->expects($hasLogger ? $this->once() : $this->never())
            ->method('log')
            ->with($json);
        $this->expectsVerifierShutdown($this->handle);
        $this->verifier = new Verifier($this->config, $hasLogger ? $this->logger : null, $this->client);
        $this->assertSame($success, $this->verifier->verify());
    }
}
