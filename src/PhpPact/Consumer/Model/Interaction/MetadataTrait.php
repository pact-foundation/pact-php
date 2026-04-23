<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

trait MetadataTrait
{
    /**
     * @var array<string, string>
     */
    private array $metadata = [];

    /**
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<string, string|MatcherInterface> $metadata
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
     * @throws JsonException
     */
    private function setMetadataValue(string $key, string|MatcherInterface $value): void
    {
        $this->metadata[$key] = is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR);
    }
}
