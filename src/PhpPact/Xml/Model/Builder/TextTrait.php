<?php

namespace PhpPact\Xml\Model\Builder;

use PhpPact\Xml\Model\Matcher\Generator;
use PhpPact\Xml\Model\Matcher\Matcher;
use PhpPact\Xml\XmlText;

trait TextTrait
{
    public function content(string|int|float $content): callable
    {
        return fn (XmlText $text) => $text->setContent($content);
    }

    public function matcher(callable ...$options): callable
    {
        return fn (XmlText $text) => $text->setMatcher(new Matcher(...$options));
    }

    public function generator(callable ...$options): callable
    {
        return fn (XmlText $text) => $text->setGenerator(new Generator(...$options));
    }

    public function contentLike(string|int|float $content): callable
    {
        return function (XmlText $text) use ($content): void {
            $text->setContent($content);
            $text->setMatcher(new Matcher(
                fn (Matcher $matcher) => $matcher->setType('type')
            ));
        };
    }
}
