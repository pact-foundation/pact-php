<?php

namespace PhpPact\Xml;

use JsonSerializable;
use PhpPact\Xml\Model\Builder\ElementTrait;
use PhpPact\Xml\Model\Builder\TextTrait;

class XmlBuilder implements JsonSerializable
{
    use ElementTrait;
    use TextTrait;

    public function __construct(private string $version, private string $charset)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'version' => $this->version,
            'charset' => $this->charset,
            'root' => $this->root,
        ];
    }
}
