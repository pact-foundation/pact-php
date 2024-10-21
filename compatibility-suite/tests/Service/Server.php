<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\VerifyResult;
use Psr\Http\Message\UriInterface;

final class Server implements ServerInterface
{
    private MockServerConfigInterface $config;
    private InteractionDriverInterface $driver;
    private VerifyResult $verifyResult;
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
            ->setPactFileWriteMode(WriteMode::OVERWRITE);

        if ($level = \getenv('PACT_LOGLEVEL')) {
            $this->config->setLogLevel($level);
        }

        $this->driver = (new InteractionDriverFactory())->create($this->config);
    }

    public function register(int ...$ids): void
    {
        $interactions = array_map(fn (int $id) => $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $id), $ids);
        foreach ($interactions as $index => $interaction) {
            $startMockServer = $index === count($interactions) - 1;
            $this->driver->registerInteraction($interaction, $startMockServer);
        }
    }

    public function getBaseUri(): UriInterface
    {
        return $this->config->getBaseUri();
    }

    public function verify(): void
    {
        $result = $this->driver->verifyInteractions();
        $this->verifyResult = new VerifyResult($result->matched, $result->mismatches);
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

    public function __destruct()
    {
        // Clean up mock server
        $this->getVerifyResult();
    }
}
