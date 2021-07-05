<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Countable;
use Iterator;

class ConsumerVersionSelectors implements Iterator, Countable
{
    /** @var int */
    private $position = 0;

    /** @var string[]  */
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

    public function current()
    {
        return $this->selectors[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->selectors[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return count($this->selectors);
    }
}
