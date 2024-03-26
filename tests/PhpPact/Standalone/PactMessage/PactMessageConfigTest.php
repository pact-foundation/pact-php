<?php

namespace PhpPactTest\Standalone\PactMessage;

use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PhpPactTest\Config\PactConfigTest;

class PactMessageConfigTest extends PactConfigTest
{
    protected function setUp(): void
    {
        $this->config = new PactMessageConfig();
    }
}
