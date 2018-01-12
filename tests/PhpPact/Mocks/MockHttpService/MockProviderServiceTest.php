<?php

namespace PhpPact\Mocks\MockHttpService\MockProviderService;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

class MockProviderServiceTest extends TestCase
{
    public function testWith()
    {
        $mockProviderService = new \PhpPact\Mocks\MockHttpService\MockProviderService('TestProvider', new \PhpPact\PactConfig());

        // test happy path with GET
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, '/', []);

        $hasException = false;

        try {
            $mockProviderService->with($request);
        } catch (Exception $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, 'Happy path with a get request');

        // test unhappy path when not set
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::NOTSET, '/', []);

        $hasException = false;

        try {
            $mockProviderService->with($request);
        } catch (\InvalidArgumentException $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException, 'An exception is thrown if the method is not set');

        // test unhappy path when request is null
        $request      = false;
        $hasException = false;

        try {
            $mockProviderService->with($request);
        } catch (\InvalidArgumentException $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException, 'An exception is thrown if the object is not a ProviderServiceRequest');
    }
}
