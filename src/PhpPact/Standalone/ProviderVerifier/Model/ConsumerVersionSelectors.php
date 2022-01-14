<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Countable;
use Iterator;

class ConsumerVersionSelectors implements Iterator, Countable
{
    /** @var int */
    private $position = 0;

    /** @var string[] */
    private $selectors = [];

    /**
     * @param string[] $selectors
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
    public function key()
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->selectors[$this->position]);
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return \count($this->selectors);
    }
}
