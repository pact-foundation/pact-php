<?php

namespace PhpPact\Xml;

use PhpPact\Xml\Model\Builder\ElementTrait;
use PhpPact\Xml\Model\Builder\GeneratorTrait;
use PhpPact\Xml\Model\Builder\MatcherTrait;
use PhpPact\Xml\Model\Builder\TextTrait;

class XmlBuilder
{
    use ElementTrait;
    use TextTrait;
    use MatcherTrait;
    use GeneratorTrait;

    public function __construct(private string $version, private string $charset)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getArray(): array
    {
        return [
            'version' => $this->version,
            'charset' => $this->charset,
            'root' => $this->root->getArray(),
        ];
    }
}
