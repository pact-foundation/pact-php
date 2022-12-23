CHANGELOG
=========

9.0
---

* General
  * [BC BREAK] Declare types for:
    * Properties
    * Arguments
    * Return values

* Matchers
  * [BC BREAK] Update matcher implementations

* Installers
  * [BC BREAK] Removed `PhpPact\Standalone\Installer\Model\Scripts::getMockService`
  * Added `PhpPact\Standalone\Installer\Model\Scripts::getCode`
  * Added `PhpPact\Standalone\Installer\Model\Scripts::getLibrary`

* Config
  * [BC BREAK] Updated `PhpPact\Standalone\MockService\MockServerConfigInterface`, removed these methods:
    * `hasCors`
    * `setCors`
    * `setHealthCheckTimeout`
    * `getHealthCheckTimeout`
    * `setHealthCheckRetrySec`
    * `getHealthCheckRetrySec`
  * [BC BREAK] Removed environments variables:
    * PACT_CORS
    * PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT
    * PACT_MOCK_SERVER_HEALTH_CHECK_RETRY_SEC
  * Moved these methods from `PhpPact\Standalone\MockService\MockServerConfigInterface` to `PhpPact\Standalone\PactConfigInterface`:
    * `getPactFileWriteMode`
    * `setPactFileWriteMode`
  * `PhpPact\Standalone\MockService\MockServerConfigInterface` now extends `PhpPact\Standalone\PactConfigInterface`
  * Added `PhpPact\Standalone\PactConfig`
  * `PhpPact\Standalone\PactMessage\PactMessageConfig` now extends `PhpPact\Standalone\PactConfig`
  * `PhpPact\Standalone\MockService\MockServerConfig` now extends `PhpPact\Standalone\PactConfig`
  * Change default specification version to `3.0.0`

* Mock Server
  * [BC BREAK] Removed `PhpPact\Standalone\MockService\MockServer`
  * [BC BREAK] Removed `PhpPact\Standalone\MockService\Service\MockServerHttpService`
  * [BC BREAK] Removed `PhpPact\Standalone\MockService\Service\MockServerHttpServiceInterface`
  * [BC BREAK] Removed `PhpPact\Standalone\Exception\HealthCheckFailedException`
  * Added `PhpPact\Consumer\Exception\MockServerNotStartedException`

* Interaction Builder
  * Added `PhpPact\Consumer\InteractionBuilder::createMockServer`
  * [BC BREAK] It's now required to call `PhpPact\Consumer\InteractionBuilder::createMockServer` manually before `PhpPact\Consumer\InteractionBuilder::verify`
  * [BC BREAK] Removed `PhpPact\Consumer\InteractionBuilder::finalize`
  * [BC BREAK] `PhpPact\Consumer\Model\ConsumerRequest::getQuery` now return `array` instead of `string`
  * [BC BREAK] `PhpPact\Consumer\Model\ConsumerRequest::setQuery` now accept argument `array` instead of `string`
  * Added `PhpPact\Consumer\Exception\InteractionRequestBodyNotAddedException`
  * Added `PhpPact\Consumer\Exception\InteractionResponseBodyNotAddedException`
  * Added `PhpPact\Consumer\Exception\PactFileNotWroteException`

* PHPUnit
  * [BC BREAK] Removed `PhpPact\Consumer\Hook\ContractDownloader`
  * Replace broker http client by broker cli in `PhpPact\Consumer\Listener\PactTestListener`
