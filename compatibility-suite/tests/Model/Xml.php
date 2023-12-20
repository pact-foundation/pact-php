<?php

namespace PhpPactTest\CompatibilitySuite\Model;

use PhpPact\Consumer\Model\Body\Text;
use SimpleXMLElement;

class Xml extends Text
{
    public function getContents(): string
    {
        $root = simplexml_load_string(parent::getContents());

        return json_encode([
            'root' => $this->xmlElementToArray($root),
        ]);
    }

    private function xmlElementToArray(SimpleXMLElement $element): array
    {
        $children = $element->children();
        if (0 !== $children->count()) {
            $items = [];
            foreach ($children as $child) {
                $items[] = $this->xmlElementToArray($child);
            }

            return [
                'name' => $element->getName(),
                'children' => $items,
            ];
        } else {
            return [
                'content' => (string) $element,
            ];
        }
    }
}
