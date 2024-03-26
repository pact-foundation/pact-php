<?php

namespace PhpPactTest\CompatibilitySuite\Model;

class MatchingRule
{
    public function __construct(
        private string $matcher,
        private string $category,
        private string $subCategory,
        private array $matcherAttributes
    ) {
    }

    public function getMatcher(): string
    {
        return $this->matcher;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getSubCategory(): string
    {
        return $this->subCategory;
    }

    public function getMatcherAttributes(): array
    {
        return $this->matcherAttributes;
    }

    public function getMatcherAttribute(string $attribute): mixed
    {
        return $this->matcherAttributes[$attribute] ?? null;
    }
}
