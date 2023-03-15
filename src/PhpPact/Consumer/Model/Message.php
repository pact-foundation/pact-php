<?php

namespace PhpPact\Consumer\Model;

use JsonException;
use PhpPact\Consumer\Model\Interaction\ContentTypeTrait;

/**
 * Message metadata and contents to be posted to the Mock Server for PACT tests.
 */
class Message
{
    use ProviderStates;
    use ContentTypeTrait;

    private string $description;

    /**
     * @var array<string, string>
     */
    private array $metadata = [];

    private ?string $contents = null;

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<string, string> $metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = [];
        foreach ($metadata as $key => $value) {
            $this->setMetadataValue($key, $value);
        }

        return $this;
    }

    private function setMetadataValue(string $key, string $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    /**
     * @throws JsonException
     */
    public function setContents(mixed $contents): self
    {
        if (\is_string($contents) || \is_null($contents)) {
            $this->contents = $contents;
        } else {
            $this->contents = \json_encode($contents, JSON_THROW_ON_ERROR);
            if (!isset($this->contentType)) {
                $this->setContentType('application/json');
            }
        }

        return $this;
    }
}
