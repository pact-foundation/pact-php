<?php

namespace PhpPact\SyncMessage;

use PhpPact\Consumer\AbstractMessageBuilder;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactory;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactoryInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

class SyncMessageBuilder extends AbstractMessageBuilder
{
    private SyncMessageDriverInterface $driver;

    public function __construct(MockServerConfigInterface $config, ?SyncMessageDriverFactoryInterface $driverFactory = null)
    {
        parent::__construct();
        $this->driver = ($driverFactory ?? new SyncMessageDriverFactory())->create($config);
    }

    public function registerMessage(): void
    {
        $this->driver->registerMessage($this->message);
    }

    public function verify(): bool
    {
        return $this->driver->verifyMessage()->matched;
    }
}
