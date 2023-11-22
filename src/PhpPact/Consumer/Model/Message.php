<?php

namespace PhpPact\Consumer\Model;

use JsonException;
use PhpPact\Consumer\Exception\BodyNotSupportedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;

/**
 * Message metadata and contents to be posted to the Mock Server for PACT tests.
 */
class Message
{
    use ProviderStates;

    private string $description;

    /**
     * @var array<string, string>
     */
    private array $metadata = [];

    private Text|Binary|null $contents = null;

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
     * @param array<string, string|array<string, mixed>> $metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = [];
        foreach ($metadata as $key => $value) {
            $this->setMetadataValue($key, $value);
        }

        return $this;
    }

    /**
     * @param string|array<string, mixed> $value
     *
     * @throws JsonException
     */
    private function setMetadataValue(string $key, string|array $value): void
    {
        $this->metadata[$key] = is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR);
    }

    public function getContents(): Text|Binary|null
    {
        return $this->contents;
    }

    /**
     * @throws JsonException
     */
    public function setContents(mixed $contents): self
    {
        if (\is_string($contents)) {
            $this->contents = new Text($contents, 'text/plain');
        } elseif (\is_null($contents) || $contents instanceof Text || $contents instanceof Binary) {
            $this->contents = $contents;
        } elseif ($contents instanceof Multipart) {
            throw new BodyNotSupportedException('Message does not support multipart');
        } else {
            $this->contents = new Text(\json_encode($contents, JSON_THROW_ON_ERROR), 'application/json');
        }

        return $this;
    }
}
