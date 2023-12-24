<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistry;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\Logger;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\VerifyResult;
use Psr\Http\Message\UriInterface;

final class Server implements ServerInterface
{
    private MockServerConfigInterface $config;
    private PactDriverInterface $pactDriver;
    private InteractionRegistryInterface $interactionRegistry;
    private MockServerInterface $mockServer;
    private VerifyResult $verifyResult;
    private Logger $logger;
    private PactPath $pactPath;

    public function __construct(
        string $specificationVersion,
        private InteractionsStorageInterface $storage
    ) {
        $this->pactPath = new PactPath(sprintf('server_specification_%s', $specificationVersion));
        $this->config = new MockServerConfig();
        $this->config
            ->setConsumer($this->pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($specificationVersion)
            ->setPactFileWriteMode(PactConfigInterface::MODE_OVERWRITE);

        $this->logger = new Logger();

        $client = new Client();
        $pactRegistry = new PactRegistry($client);
        $this->pactDriver = new PactDriver($client, $this->config, $pactRegistry);
        $this->mockServer = new MockServer($client, $pactRegistry, $this->config, $this->logger);
        $this->interactionRegistry = new InteractionRegistry($client, $pactRegistry);
    }

    public function register(int ...$ids): void
    {
        $interactions = array_map(fn (int $id) => $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $id), $ids);
        $this->pactDriver->setUp();
        foreach ($interactions as $interaction) {
            $this->interactionRegistry->registerInteraction($interaction);
        }
        $this->mockServer->start();
    }

    public function getBaseUri(): UriInterface
    {
        return $this->config->getBaseUri();
    }

    public function verify(): void
    {
        $success = $this->mockServer->verify();
        $this->verifyResult = new VerifyResult($success, !$success ? $this->logger->getOutput() : '');
    }

    public function getVerifyResult(): VerifyResult
    {
        if (!isset($this->verifyResult)) {
            $this->verify();
        }

        return $this->verifyResult;
    }

    public function getPactPath(): PactPath
    {
        return $this->pactPath;
    }

    public function getPort(): int
    {
        return $this->config->getPort();
    }
}
