<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Service\FFIInterface;

abstract class AbstractDriver implements DriverInterface
{
    protected int $id;

    public function __construct(
        protected FFIInterface $ffi,
        protected PactDriverInterface $pactDriver
    ) {
    }

    public function setBody(string $part, ?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->ffi->call('pactffi_with_body', $this->id, $this->ffi->get($part), $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    public function setDescription(string $description): void
    {
        $this->ffi->call('pactffi_upon_receiving', $this->id, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderStates(array $providerStates): void
    {
        foreach ($providerStates as $providerState) {
            $this->ffi->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->ffi->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }
}
