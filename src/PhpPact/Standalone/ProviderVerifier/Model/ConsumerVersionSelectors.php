<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Countable;
use Iterator;
use PhpPact\Standalone\ProviderVerifier\Model\Selector\SelectorInterface;
use JsonException;

/**
 * @implements Iterator<int, string>
 */
class ConsumerVersionSelectors implements Iterator, Countable
{
    private int $position = 0;

    /** @var array<int, string> */
    private array $selectors = [];

    /**
     * @param array<int, string|SelectorInterface> $selectors
     */
    public function __construct(array $selectors = [])
    {
        $this->setSelectors($selectors);
    }

    /**
     * @param array<int, string|SelectorInterface> $selectors
     */
    public function setSelectors(array $selectors): self
    {
        $this->selectors = [];
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function addSelector(string|SelectorInterface $selector): self
    {
        $this->selectors[] = $selector instanceof SelectorInterface ? json_encode($selector, JSON_THROW_ON_ERROR) : $selector;

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
