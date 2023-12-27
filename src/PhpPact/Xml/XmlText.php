<?php

namespace PhpPact\Xml;

use PhpPact\Xml\Model\Matcher\Generator;
use PhpPact\Xml\Model\Matcher\Matcher;

class XmlText
{
    private string|int|float $content;

    private ?Matcher $matcher = null;

    private ?Generator $generator = null;

    public function __construct(callable ...$options)
    {
        array_walk($options, fn (callable $option) => $option($this));
    }

    public function setContent(string|int|float $content): self
    {
        $this->content = $content;

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

    /**
     * @return array<string, mixed>
     */
    public function getArray(): array
    {
        $result = $this->getBaseArray();

        if ($this->matcher) {
            $result['matcher'] = $this->matcher->getArray();
        }

        if ($this->generator) {
            $generator = $this->generator->getArray();
            $result['pact:generator:type'] = $generator['pact:generator:type'];
            $result['matcher'] += array_diff_key($generator, array_flip(['pact:generator:type']));
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function getBaseArray(): array
    {
        return [
            'content' => $this->content,
        ];
    }
}
