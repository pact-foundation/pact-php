<?php

namespace PhpPact\Xml\Model\Builder;

use PhpPact\Consumer\Matcher\Formatters\Xml\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Xml\XmlElement;
use PhpPact\Xml\XmlText;

trait TextTrait
{
    public function content(string|float|int|bool|null|MatcherInterface $content): callable
    {
        return fn (XmlElement $element) => $element->setText(new XmlText($content));
    }

    public function contentLike(string|float|int|bool|null $content): callable
    {
        return function (XmlElement $element) use ($content): void {
            $matcher = new Type($content);
            $text = new XmlText($matcher->withFormatter(new XmlContentFormatter()));
            $element->setText($text);
        };
    }
}
