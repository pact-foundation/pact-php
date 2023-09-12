<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;

trait BodyTrait
{
    private Text|Binary|Multipart|null $body = null;

    public function getBody(): Text|Binary|Multipart|null
    {
        return $this->body;
    }

    /**
     * @throws JsonException
     */
    public function setBody(mixed $body): self
    {
        if (\is_string($body)) {
            $this->body = new Text($body, 'text/plain');
        } elseif (\is_null($body) || $body instanceof Text || $body instanceof Binary || $body instanceof Multipart) {
            $this->body = $body;
        } else {
            $this->body = new Text(\json_encode($body, JSON_THROW_ON_ERROR), 'application/json');
        }

        return $this;
    }
}
