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

    /** @var array<int, string>> */
    private array $selectors;

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

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->selectors[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        ++$this->position;
    }

    #[\ReturnTypeWillChange]
    public function key(): int
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function valid(): bool
    {
        return isset($this->selectors[$this->position]);
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return \count($this->selectors);
    }
}
