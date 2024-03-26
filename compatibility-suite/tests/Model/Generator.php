<?php

namespace PhpPactTest\CompatibilitySuite\Model;

class Generator
{
    public function __construct(
        private string $generator,
        private string $category,
        private ?string $subCategory,
        private array $generatorAttributes
    ) {
    }

    public function getGenerator(): string
    {
        return $this->generator;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getSubCategory(): ?string
    {
        return $this->subCategory;
    }

    public function getGeneratorAttributes(): array
    {
        return $this->generatorAttributes;
    }
}
