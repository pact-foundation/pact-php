<?php

namespace PhpPact\Standalone\MockService;

class MockServerEnvConfig extends MockServerConfig
{
    public function __construct()
    {
        $this
            ->setHost(\getenv('PACT_MOCK_SERVER_HOST'))
            ->setPort(\getenv('PACT_MOCK_SERVER_PORT'))
            ->setConsumer(\getenv('PACT_CONSUMER_NAME'))
            ->setProvider(\getenv('PACT_PROVIDER_NAME'))
            ->setPactDir(\getenv('PACT_OUTPUT_DIR'));
    }
}
