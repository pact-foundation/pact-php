<?php

namespace PhpPact\Xml\Model\Builder;

use PhpPact\Consumer\Matcher\Formatters\Xml\XmlElementFormatter;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Xml\XmlElement;

trait ElementTrait
{
    private XmlElement $root;

    public function root(callable ...$options): void
    {
        $this->root = new XmlElement(...$options);
    }

    public function examples(int $examples): callable
    {
        return fn (XmlElement $element) => $element->setExamples($examples);
    }

    public function add(callable ...$options): callable
    {
        return fn (XmlElement $element) => $element->addChild(new XmlElement(...$options));
    }

    public function name(string $name): callable
    {
        return fn (XmlElement $element) => $element->setName($name);
    }

    public function attribute(string $name, string|float|int|bool|MatcherInterface $value): callable
    {
        return fn (XmlElement $element) => $element->addAttribute($name, $value);
    }

    public function eachLike(callable ...$options): callable
    {
        return function (XmlElement $element) use ($options): void {
            $child = new XmlElement(...$options);
            $matcher = new Type($child);
            $element->addChild($matcher->withFormatter(new XmlElementFormatter()));
        };
    }
}
