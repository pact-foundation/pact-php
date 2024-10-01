<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Selector;

use PhpPact\Standalone\ProviderVerifier\Exception\InvalidSelectorValueException;

class Selector implements SelectorInterface
{
    public function __construct(
        private ?bool $mainBranch = null,
        private ?string $branch = null,
        private ?string $fallbackBranch = null,
        private ?bool $matchingBranch = null,
        private ?string $tag = null,
        private ?string $fallbackTag = null,
        private ?bool $deployed = null,
        private ?bool $released = null,
        private ?bool $deployedOrReleased = null,
        private ?string $environment = null,
        private ?bool $latest = null,
        private ?string $consumer = null,
    ) {
        foreach (get_object_vars($this) as $key => $value) {
            if (false === $value && 'latest' !== $key) {
                throw new InvalidSelectorValueException(sprintf("Value 'false' is not allowed for selector %s", $key));
            }
        }
    }

    /**
     * @return array<string, string|bool>
     */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $value) => is_bool($value) || is_string($value));
    }
}
