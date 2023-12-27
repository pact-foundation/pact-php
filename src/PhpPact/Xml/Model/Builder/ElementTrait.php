<?php

namespace PhpPact\Xml\Model\Builder;

use PhpPact\Xml\Model\Matcher\Matcher;
use PhpPact\Xml\XmlElement;
use PhpPact\Xml\XmlText;

trait ElementTrait
{
    private XmlElement $root;

    public function root(callable ...$options): void
    {
        $this->root = new XmlElement(...$options);
    }

    public function add(callable ...$options): callable
    {
        return fn (XmlElement $element) => $element->addChild(new XmlElement(...$options));
    }

    public function name(string $name): callable
    {
        return fn (XmlElement $element) => $element->setName($name);
    }

    public function text(callable ...$options): callable
    {
        return fn (XmlElement $element) => $element->setText(new XmlText(...$options));
    }

    public function attribute(string $name, mixed $value): callable
    {
        return fn (XmlElement $element) => $element->addAttribute($name, $value);
    }

    public function eachLike(int $min = 1, ?int $max = null, int $examples = 1): callable
    {
        return function (XmlElement $element) use ($min, $max, $examples): void {
            $options = [
                'min' => $min,
                'examples' => $examples,
            ];
            if (isset($max)) {
                $options['max'] = $max;
            }
            $element->setMatcher(new Matcher(
                fn (Matcher $matcher) => $matcher->setType('type'),
                fn (Matcher $matcher) => $matcher->setOptions($options),
            ));
        };
    }
}
