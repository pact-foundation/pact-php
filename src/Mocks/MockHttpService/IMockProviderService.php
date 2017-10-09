<?php

namespace PhpPact\Mocks\MockHttpService;

use PhpPact\Mocks\IMockProvider;

interface IMockProviderService extends IMockProvider
{
    public function With($request);

    public function WillRespondWith($response);

    public function Start();

    public function Stop();

    public function ClearInteractions();

    public function VerifyInteractions();

    public function SendMockRequest(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $providerServiceRequest, $baseUri);
}
