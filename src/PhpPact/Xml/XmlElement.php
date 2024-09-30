<?php

namespace PhpPact\Xml;

use JsonSerializable;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Xml\Exception\InvalidXmlElementException;

class XmlElement implements JsonSerializable
{
    private string $name;

    /**
     * @var array<XmlElement|MatcherInterface>
     */
    private array $children = [];

    /**
     * @var array<string, string|float|int|bool|MatcherInterface>
     */
    private array $attributes = [];

    private ?XmlText $text = null;

    private ?int $examples = null;

    public function __construct(callable ...$options)
    {
        array_walk($options, fn (callable $option) => $option($this));
        if (!isset($this->name)) {
            throw new InvalidXmlElementException("Xml element's name is required");
        }
    }

    public function setName(string $name): self
    {
        if (preg_match('/^[0-9]+|[^a-zA-Z0-9\-\_\:]+/', $name) !== 0) {
            throw new InvalidXmlElementException("Xml element's name is invalid");
        }
        $this->name = $name;

        return $this;
    }

    public function addChild(self|MatcherInterface $child): self
    {
        $this->children[] = $child;

        return $this;
    }

    public function setText(?XmlText $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function addAttribute(string $name, string|float|int|bool|MatcherInterface $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $result = [
            'name' => $this->name,
            'children' => $this->children,
            'attributes' => $this->attributes,
        ];

        if (null !== $this->examples) {
            $result['examples'] = $this->examples;
        }

        if ($this->text) {
            $result['children'][] = $this->text;
        }

        return $result;
    }

    public function setExamples(?int $examples): self
    {
        $this->examples = $examples;

        return $this;
    }

    public function getExamples(): ?int
    {
        return $this->examples;
    }
}
