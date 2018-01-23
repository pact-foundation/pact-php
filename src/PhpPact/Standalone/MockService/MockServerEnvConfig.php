<?php

namespace PhpPact\Standalone\MockService;

class MockServerEnvConfig extends MockServerConfig
{
    public function __construct()
    {
        parent::__construct(
            \getenv('PACT_MOCK_SERVER_HOST'),
            \getenv('PACT_MOCK_SERVER_PORT'),
            \getenv('PACT_CONSUMER_NAME'),
            \getenv('PACT_PROVIDER_NAME')
        );
    }
}
