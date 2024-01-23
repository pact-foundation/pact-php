<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\GeneratorNotRequiredException;
use PhpPact\Consumer\Matcher\Exception\GeneratorRequiredException;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

abstract class GeneratorAwareMatcher extends AbstractMatcher implements GeneratorAwareInterface
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

    /**
     * @return string|array<string, mixed>
     */
    public function jsonSerialize(): string|array
    {
        if (null === $this->getValue()) {
            if (!$this->generator) {
                throw new GeneratorRequiredException(sprintf("Generator is required for matcher '%s' when example value is not set", $this->getType()));
            }
        } elseif ($this->generator) {
            throw new GeneratorNotRequiredException(sprintf("Generator '%s' is not required for matcher '%s' when example value is set", $this->generator->getType(), $this->getType()));
        }

        return $this->getFormatter()->format($this);
    }
}
