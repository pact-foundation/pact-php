<?php

namespace PhpPact\Consumer\Matcher\Formatters\Xml;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Xml\XmlElement;

class XmlElementFormatter implements FormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        $value = $matcher->getValue();
        if (!$value instanceof XmlElement) {
            throw new InvalidValueException('Value must be xml element');
        }

        $result = [
            'pact:matcher:type' => $matcher->getType(),
            ...$matcher->getAttributes()->getData(),
            'value' => $matcher->getValue(),
        ];
        $examples = $value->getExamples();

        if (null !== $examples) {
            $result['examples'] = $examples;
            $value->setExamples(null);
        }

        return $result;
    }
}
