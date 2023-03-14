<?php

namespace PhpPact\Consumer\Model;

use FFI;
use PhpPact\Consumer\Exception\InteractionRequestBodyNotAddedException;
use PhpPact\Consumer\Exception\InteractionResponseBodyNotAddedException;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\PactConfigInterface;

/**
 * Class Pact.
 */
class Pact extends AbstractPact
{
    /**
     * @param MockServerConfigInterface $config
     */
    public function __construct(private MockServerConfigInterface $config)
    {
        parent::__construct();
        $this
            ->initWithLogLevel()
            ->newPact()
            ->withSpecification();
    }

    private function createMockServer(): void
    {
        $port = $this->ffi->pactffi_create_mock_server_for_transport(
            $this->id,
            $this->config->getHost(),
            $this->config->getPort(),
            $this->config->isSecure() ? 'https' : 'http',
            null
        );

        if ($port < 0) {
            throw new MockServerNotStartedException($port);
        }
        $this->config->setPort($port);
    }

    public function verifyInteractions(): bool
    {
        $result = $this->ffi->pactffi_mock_server_matched($this->config->getPort());

        if ($result) {
            $error = $this->ffi->pactffi_write_pact_file(
                $this->config->getPort(),
                $this->config->getPactDir(),
                $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
            );
            if ($error) {
                throw new PactFileNotWroteException($error);
            }
        }

        $this->ffi->pactffi_cleanup_mock_server($this->config->getPort());
        $this->ffi->pactffi_free_pact_handle($this->id);

        return $result;
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this
            ->newInteraction($interaction)
            ->given($interaction)
            ->uponReceiving($interaction)
            ->with($interaction)
            ->willRespondWith($interaction)
            ->createMockServer();

        return true;
    }

    private function initWithLogLevel(): self
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->ffi->pactffi_init_with_log_level($logLevel);
        }

        return $this;
    }

    private function newPact(): self
    {
        $this->id = $this->ffi->pactffi_new_pact($this->config->getConsumer(), $this->config->getProvider());

        return $this;
    }

    private function withSpecification(): self
    {
        $supportedVersions = [
            '1.0.0' => $this->ffi->PactSpecification_V1,
            '1.1.0' => $this->ffi->PactSpecification_V1_1,
            '2.0.0' => $this->ffi->PactSpecification_V2,
            '3.0.0' => $this->ffi->PactSpecification_V3,
            '4.0.0' => $this->ffi->PactSpecification_V4,
        ];
        $version = $this->config->getPactSpecificationVersion();
        if (isset($supportedVersions[$version])) {
            $specification = $supportedVersions[$version];
        } else {
            trigger_error(sprintf("Specification version '%s' is unknown", $version), E_USER_WARNING);
            $specification = $this->ffi->PactSpecification_Unknown;
        }
        $this->ffi->pactffi_with_specification($this->id, $specification);

        return $this;
    }

    private function newInteraction(Interaction $interaction): self
    {
        $id = $this->ffi->pactffi_new_interaction($this->id, $interaction->getDescription());
        $interaction->setId($id);

        return $this;
    }

    private function given(Interaction $interaction): self
    {
        $this->ffi->pactffi_given($interaction->getId(), $interaction->getProviderState());

        return $this;
    }

    private function uponReceiving(Interaction $interaction): self
    {
        $this->ffi->pactffi_upon_receiving($interaction->getId(), $interaction->getDescription());

        return $this;
    }

    private function with(Interaction $interaction): self
    {
        $id = $interaction->getId();
        $request = $interaction->getRequest();
        $this->ffi->pactffi_with_request($id, $request->getMethod(), $request->getPath());
        foreach ($request->getHeaders() as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_header_v2($id, $this->ffi->InteractionPart_Request, $header, $index, $value);
            }
        }
        foreach ($request->getQuery() as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_query_parameter_v2($id, $key, $index, $value);
            }
        }
        if (!\is_null($request->getBody())) {
            $success = $this->ffi->pactffi_with_body($id, $this->ffi->InteractionPart_Request, null, $request->getBody());
            if (!$success) {
                throw new InteractionRequestBodyNotAddedException();
            }
        }

        return $this;
    }

    private function willRespondWith(Interaction $interaction): self
    {
        $id = $interaction->getId();
        $response = $interaction->getResponse();
        $this->ffi->pactffi_response_status($id, $response->getStatus());
        foreach ($response->getHeaders() as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_header_v2($id, $this->ffi->InteractionPart_Response, $header, $index, $value);
            }
        }
        if (!\is_null($response->getBody())) {
            $success = $this->ffi->pactffi_with_body($id, $this->ffi->InteractionPart_Response, null, $response->getBody());
            if (!$success) {
                throw new InteractionResponseBodyNotAddedException();
            }
        }

        return $this;
    }
}
