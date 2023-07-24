<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;

trait BodyTrait
{
    use ContentTypeTrait;

    private ?string $body = null;

    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param array<mixed>|string|null $body
     *
     * @throws JsonException
     */
    public function setBody(array|string|null $body): self
    {
        if (\is_string($body) || \is_null($body)) {
            $this->body = $body;
        } else {
            $this->body = \json_encode($body, JSON_THROW_ON_ERROR);
            if (!isset($this->contentType)) {
                $this->setContentType('application/json');
            }
        }

        return $this;
    }
}
