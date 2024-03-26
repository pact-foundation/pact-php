<?php

namespace PhpPact\Consumer\Matcher\Model;

use PhpPact\Consumer\Matcher\Exception\AttributeConflictException;

class Attributes
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private GeneratorInterface|MatcherInterface $parent, private array $data = [])
    {
    }

    public function getParent(): GeneratorInterface|MatcherInterface
    {
        return $this->parent;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function merge(self $attributes): self
    {
        foreach ($this->data as $key => $value) {
            if ($attributes->has($key) && $value !== $attributes->get($key)) {
                throw new AttributeConflictException(sprintf("Attribute '%s' of %s '%s' and %s '%s' are conflict", $key, $this->getParentType(), $this->getParentName(), $attributes->getParentType(), $attributes->getParentName()));
            }
        }

        return new self($this->parent, $this->data + $attributes->getData());
    }

    private function getParentType(): string
    {
        return $this->parent instanceof GeneratorInterface ? 'generator' : 'matcher';
    }

    private function getParentName(): string
    {
        return $this->parent->getType();
    }
}
