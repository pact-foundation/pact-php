<?php

namespace PhpPact\Consumer\Driver\Pact;

use Composer\Semver\Comparator;
use PhpPact\Config\Enum\WriteMode;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Exception\MissingPactException;
use PhpPact\Consumer\Driver\Exception\PactFileNotWrittenException;
use PhpPact\Consumer\Driver\Exception\PactNotModifiedException;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\FFI\ClientInterface;

class PactDriver implements PactDriverInterface
{
    protected ?Pact $pact = null;

    public function __construct(
        protected ClientInterface $client,
        protected PactConfigInterface $config
    ) {
    }

    public function cleanUp(): void
    {
        $success = $this->client->freePactHandle($this->getPact()->handle) === 0;
        if (!$success) {
            trigger_error('Can not free pact handle. The handle is not valid or does not refer to a valid Pact. Could be that it was previously deleted.', E_USER_WARNING);
        }
        $this->pact = null;
    }

    public function writePact(): void
    {
        $error = $this->client->pactHandleWriteFile(
            $this->getPact()->handle,
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === WriteMode::OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWrittenException($error);
        }
    }

    public function getPact(): Pact
    {
        if (!$this->pact) {
            throw new MissingPactException();
        }

        return $this->pact;
    }

    public function setUp(): void
    {
        if ($this->pact) {
            return;
        }
        $this->initWithLogLevel();
        $this->newPact();
        $this->withSpecification();
    }

    protected function getSpecification(): int
    {
        return match (true) {
            $this->versionEqualTo('1.0.0') => $this->client->getPactSpecificationV1(),
            $this->versionEqualTo('1.1.0') => $this->client->getPactSpecificationV1_1(),
            $this->versionEqualTo('2.0.0') => $this->client->getPactSpecificationV2(),
            $this->versionEqualTo('3.0.0') => $this->client->getPactSpecificationV3(),
            $this->versionEqualTo('4.0.0') => $this->client->getPactSpecificationV4(),
            default => call_user_func(function () {
                trigger_error(sprintf("Specification version '%s' is unknown", $this->config->getPactSpecificationVersion()), E_USER_WARNING);

                return $this->client->getPactSpecificationUnknown();
            }),
        };
    }

    private function versionEqualTo(string $version): bool
    {
        return Comparator::equalTo($this->config->getPactSpecificationVersion(), $version);
    }

    private function initWithLogLevel(): void
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->client->initWithLogLevel($logLevel);
        }
    }

    private function newPact(): void
    {
        $this->pact = new Pact($this->client->newPact($this->config->getConsumer(), $this->config->getProvider()));
    }

    private function withSpecification(): void
    {
        $success = $this->client->withSpecification($this->getPact()->handle, $this->getSpecification());
        if (!$success) {
            throw new PactNotModifiedException("The pact can't be modified (i.e. the mock server for it has already started, or the version is invalid)");
        }
    }
}
