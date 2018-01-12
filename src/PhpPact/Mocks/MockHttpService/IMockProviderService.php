<?php

namespace PhpPact\Mocks\MockHttpService;

use PhpPact\Mocks\IMockProvider;

interface IMockProviderService extends IMockProvider
{
    public function with($request);

    public function willRespondWith($response);

    public function start();

    public function stop();

    public function clearInteractions();

    public function verifyInteractions();

    public function sendMockRequest(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $providerServiceRequest, $baseUri);
}
