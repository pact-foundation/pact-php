<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Countable;
use Iterator;

/**
 * @implements Iterator<int, string>
 */
class ConsumerVersionSelectors implements Iterator, Countable
{
    private int $position = 0;

    /** @var array<int, string> */
    private array $selectors = [];

    /**
     * @param array<int, string> $selectors
     */
    public function __construct(array $selectors = [])
    {
        $this->selectors = $selectors;
    }

    public function addSelector(string $selector): self
    {
        $this->selectors[] = $selector;

        return $this;
    }

    public function current(): string
    {
        return $this->selectors[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->selectors[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return \count($this->selectors);
    }
}
