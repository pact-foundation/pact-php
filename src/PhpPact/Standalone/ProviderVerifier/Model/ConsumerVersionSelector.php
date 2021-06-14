<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use JsonSerializable;

class ConsumerVersionSelector implements JsonSerializable
{
    /** @var string */
    private $pacticipant;

    /** @var string */
    private $tag;

    /** @var string */
    private $version;

    /** @var bool */
    private $latest;

    /** @var bool */
    private $all;

    public function __construct(string $pacticipant, string $tag, string $version, bool $latest, bool $all)
    {
        $this->pacticipant = $pacticipant;
        $this->tag = $tag;
        $this->version = $version;
        $this->latest = $latest;
        $this->all = $all;
    }

    public function isValid(): bool
    {
        switch (true) {
            case $this->pacticipant !== "" && $this->tag === "":
            case $this->all && $this->latest:
            case $this->all !== "" && $this->pacticipant !== "":
                return false;
        }

        return true;
    }

    public function jsonSerialize()
    {
        $data = [
            'pacticipant' => $this->pacticipant,
            'tag' => $this->tag,
            'version' => $this->version,
            'latest' => $this->latest,
            'all' => $this->all,
        ];

        return array_filter($data);
    }
}