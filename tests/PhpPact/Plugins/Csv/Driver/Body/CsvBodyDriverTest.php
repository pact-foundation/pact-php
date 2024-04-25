<?php

namespace PhpPactTest\Plugins\Csv\Driver\Body;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Plugin\Driver\Body\PluginBodyDriverInterface;
use PhpPact\Plugins\Csv\Driver\Body\CsvBodyDriver;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CsvBodyDriverTest extends TestCase
{
    private InteractionBodyDriverInterface $driver;
    private PluginBodyDriverInterface&MockObject $decorated;

    public function setUp(): void
    {
        $this->decorated = $this->createMock(PluginBodyDriverInterface::class);
        $this->driver = new CsvBodyDriver($this->decorated);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testRegisterBody(InteractionPart $part): void
    {
        $interaction = new Interaction();
        $this->decorated
            ->expects($this->once())
            ->method('registerBody')
            ->with($interaction, $part);
        $this->driver->registerBody($interaction, $part);
    }
}
