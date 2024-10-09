<?php

namespace PhpPact\Consumer\Matcher\Trait;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

trait GeneratorAwareTrait
{
    private ?GeneratorInterface $generator = null;

    public function setGenerator(?GeneratorInterface $generator): void
    {
        $this->generator = $generator;
    }

    public function getGenerator(): ?GeneratorInterface
    {
        return $this->generator;
    }

    public function withGenerator(?GeneratorInterface $generator): static
    {
        $matcher = clone $this;
        $matcher->setGenerator($generator);

        return $matcher;
    }
}
