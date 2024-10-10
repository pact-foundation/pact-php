<?php

namespace PhpPact\Consumer\Matcher\Formatters\Xml;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Json\JsonFormatter;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class XmlContentFormatter extends JsonFormatter
{
    public function format(MatcherInterface $matcher): Attributes
    {
        $attributes = parent::format($matcher);
        $data = [];
        foreach ($attributes as $key => $value) {
            switch ($key) {
                case 'value':
                    if (!is_string($value) && !is_float($value) && !is_int($value) && !is_bool($value) && !is_null($value)) {
                        throw new InvalidValueException('Value of xml content must be string, float, int, bool or null');
                    }
                    $data['content'] = $value;
                    break;

                case 'pact:generator:type':
                    $data['pact:generator:type'] = $value;
                    break;
                default:
                    $data['matcher'][$key] = $value;
                    break;
            }
        }

        return new Attributes($data);
    }
}
