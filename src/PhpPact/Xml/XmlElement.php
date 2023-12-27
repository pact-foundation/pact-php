<?php

namespace PhpPact\Xml;

use PhpPact\Xml\Model\Matcher\Generator;
use PhpPact\Xml\Model\Matcher\Matcher;

class XmlElement
{
    private string $name;

    /**
     * @var XmlElement[]
     */
    private array $children = [];

    /**
     * @var array<string, mixed>
     */
    private array $attributes = [];

    private ?XmlText $text = null;

    private ?Matcher $matcher = null;

    private ?Generator $generator = null;

    public function __construct(callable ...$options)
    {
        array_walk($options, fn (callable $option) => $option($this));
    }

    public function setName(string $name): self
    {
        $this->name = preg_replace('/(^[0-9]+|[^a-zA-Z0-9\-\_\:]+)/', '', $name);

        return $this;
    }

    public function addChild(self $child): self
    {
        $this->children[] = $child;

        return $this;
    }

    public function setText(?XmlText $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setMatcher(?Matcher $matcher): self
    {
        $this->matcher = $matcher;

        return $this;
    }

    public function setGenerator(?Generator $generator): self
    {
        $this->generator = $generator;

        return $this;
    }

    public function addAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArray(): array
    {
        if ($this->matcher) {
            $result = [
                'value' => $this->getBaseArray(),
            ];
            $result += $this->matcher->getArray();

            if ($this->generator) {
                $result += $this->generator->getArray();
            }
        } else {
            $result = $this->getBaseArray();
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function getBaseArray(): array
    {
        $result = [
            'name' => $this->name,
            'children' => array_map(fn (XmlElement $element) => $element->getArray(), $this->children),
            'attributes' => $this->attributes
        ];

        if ($this->text) {
            $result['children'][] = $this->text->getArray();
        }

        return $result;
    }
}
