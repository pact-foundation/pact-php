<?php

namespace PhpPact\Consumer\Matcher\Formatters\Xml;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Json\JsonFormatter;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Xml\XmlElement;

class XmlElementFormatter extends JsonFormatter
{
    public function format(MatcherInterface $matcher): Attributes
    {
        $attributes = parent::format($matcher);
        $value = $attributes->get('value');
        if (!$value instanceof XmlElement) {
            throw new InvalidValueException('Value must be xml element');
        }

        $examples = $value->getExamples();

        if (null !== $examples) {
            $attributes->set('examples', $examples);
            $value->setExamples(null);
        }

        return $attributes;
    }
}
