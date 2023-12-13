<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\GeneratorRequiredException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

abstract class GeneratorAwareMatcher implements MatcherInterface, GeneratorAwareInterface
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
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            'pact:matcher:type' => $this->getType(),
        ];

        if (null === $this->getValue()) {
            if (!$this->generator) {
                throw new GeneratorRequiredException(sprintf("Generator is required for matcher '%s' when example value is not set", $this->getType()));
            }

            return $data + ['pact:generator:type' => $this->generator->getType()] + $this->getMergedAttributes()->getData();
        }

        return $data + $this->getAttributes()->getData() + ['value' => $this->getValue()];
    }

    protected function getAttributes(): Attributes
    {
        return new Attributes($this, $this->getAttributesData());
    }

    protected function getMergedAttributes(): Attributes
    {
        return $this->getAttributes()->merge($this->generator->getAttributes());
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function getAttributesData(): array;

    abstract protected function getValue(): mixed;
}
