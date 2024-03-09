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
    private LoggerInterface|MockObject $logger;
    private CData $handle;
    private array $calls;

    protected function setUp(): void
    {
        $this->config = new VerifierConfig();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->client = $this->createMock(ClientInterface::class);
        $this->handle = FFI::new('int');
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
            ->setProviderTags($providerTags = ['feature-x', 'master', 'test', 'prod'])
            ->setProviderVersion($providerVersion = '1.2.3')
            ->setBuildUrl($buildUrl = new Uri('http://ci/build/1'))
            ->setProviderBranch($providerBranch = 'some-branch');
        $this->config->setPublishOptions($publishOptions);
        $this->config->getConsumerFilters()
            ->setFilterConsumerNames($filterConsumerNames = ['http-consumer-1', 'http-consumer-2', 'message-consumer-2']);
        $this->config->setLogLevel($logLevel = 'info');
        $this->calls = [
            ['pactffi_verifier_new_for_application', $callingAppName, $callingAppVersion, $this->handle],
            ['pactffi_verifier_set_provider_info', $this->handle, $providerName, $providerScheme, $providerHost, $providerPort, $providerPath, null],
            ['pactffi_verifier_add_provider_transport', $this->handle, $transportProtocol, $transportPort, $transportPath, $transportScheme, null],
            ['pactffi_verifier_set_provider_state', $this->handle, (string) $stateChangeUrl, $stateChangeTearDown, $stateChangeAsBody, null],
            ['pactffi_verifier_set_filter_info', $this->handle, $filterDescription, $filterState, $filterNoState, null],
            ['pactffi_verifier_set_verification_options', $this->handle, $disableSslVerification, $requestTimeout, null],
            ['pactffi_verifier_set_publish_options', $this->handle, $providerVersion, $buildUrl, $this->isInstanceOf(CData::class), count($providerTags), $providerBranch, null],
            ['pactffi_verifier_set_consumer_filters', $this->handle, $this->isInstanceOf(CData::class), count($filterConsumerNames), null],
            ['pactffi_init_with_log_level', strtoupper($logLevel), null],
        ];
    }

    public function testConstruct(): void
    {
        $this->assertClientCalls($this->calls);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
    }

    public function testAddFile(): void
    {
        $file = '/path/to/file.json';
        $this->calls[] = ['pactffi_verifier_add_file_source', $this->handle, $file, null];
        $this->assertClientCalls($this->calls);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addFile($file));
    }

    public function testAddDirectory(): void
    {
        $directory = '/path/to/directory';
        $this->calls[] = ['pactffi_verifier_add_directory_source', $this->handle, $directory, null];
        $this->assertClientCalls($this->calls);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addDirectory($directory));
    }

    public function testAddUrl(): void
    {
        $source = new Url();
        $source
            ->setUrl($url = new Uri('http://example.test/path/to/file.json'))
            ->setToken($token = 'secret token')
            ->setUsername($username = 'my username')
            ->setPassword($password = 'secret password');
        $this->calls[] = ['pactffi_verifier_url_source', $this->handle, (string) $url, $username, $password, $token, null];
        $this->assertClientCalls($this->calls);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addUrl($source));
    }

    public function testAddBroker(): void
    {
        $consumerVersionSelectors = (new ConsumerVersionSelectors())
            ->addSelector(new Selector(tag: 'foo', latest: true))
            ->addSelector('{"tag":"bar","latest":true}');
        $source = new Broker();
        $source
            ->setUrl($url = new Uri('http://example.test/path/to/file.json'))
            ->setToken($token = 'secret token')
            ->setUsername($username = 'my username')
            ->setPassword($password = 'secret password')
            ->setEnablePending($enablePending = true)
            ->setIncludeWipPactSince($wipPactSince = '2020-01-30')
            ->setProviderTags($providerTags = ['prod', 'staging'])
            ->setProviderBranch($providerBranch = 'main')
            ->setConsumerVersionSelectors($consumerVersionSelectors)
            ->setConsumerVersionTags($consumerVersionTags = ['dev']);
        $this->calls[] = [
            'pactffi_verifier_broker_source_with_selectors',
            $this->handle,
            (string) $url,
            $username,
            $password,
            $token,
            $enablePending,
            $wipPactSince,
            $this->isInstanceOf(CData::class),
            count($providerTags),
            $providerBranch,
            $this->isInstanceOf(CData::class),
            count($consumerVersionSelectors),
            $this->isInstanceOf(CData::class),
            count($consumerVersionTags),
            null
        ];
        $this->assertClientCalls($this->calls);
        $this->verifier = new Verifier($this->config, $this->logger, $this->client);
        $this->assertSame($this->verifier, $this->verifier->addBroker($source));
    }

    #[TestWith([0, true,  false])]
    #[TestWith([0, true,  true])]
    #[TestWith([1, false, false])]
    #[TestWith([2, false, false])]
    public function testVerify(int $error, bool $success, bool $hasLogger): void
    {
        $json = '{"key": "value"}';
        $this->calls[] = ['pactffi_verifier_execute', $this->handle, $error];
        $this->logger
            ->expects($this->exactly($hasLogger))
            ->method('log')
            ->with($json);
        if ($hasLogger) {
            $this->calls[] = ['pactffi_verifier_json', $this->handle, $json];
        }
        $this->calls[] = ['pactffi_verifier_shutdown', $this->handle, null];
        $this->assertClientCalls($this->calls);
        $this->verifier = new Verifier($this->config, $hasLogger ? $this->logger : null, $this->client);
        $this->assertSame($success, $this->verifier->verify());
    }
}
