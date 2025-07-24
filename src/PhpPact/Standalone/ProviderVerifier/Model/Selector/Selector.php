<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Selector;

use PhpPact\Standalone\ProviderVerifier\Exception\InvalidSelectorValueException;

class Selector implements SelectorInterface
{
    public function __construct(
        private readonly ?bool $mainBranch = null,
        private readonly ?string $branch = null,
        private readonly ?string $fallbackBranch = null,
        private readonly ?bool $matchingBranch = null,
        private readonly ?string $tag = null,
        private readonly ?string $fallbackTag = null,
        private readonly ?bool $deployed = null,
        private readonly ?bool $released = null,
        private readonly ?bool $deployedOrReleased = null,
        private readonly ?string $environment = null,
        private readonly ?bool $latest = null,
        private readonly ?string $consumer = null,
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
