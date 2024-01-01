<?php

namespace PhpPact\Xml;

use JsonSerializable;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class XmlText implements JsonSerializable
{
    public function __construct(private string|float|int|bool|null|MatcherInterface $content)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        if ($this->content instanceof MatcherInterface) {
            return $this->content->jsonSerialize();
        }

        return [
            'content' => $this->content,
        ];
    }
}
