<?php

namespace PhpPact\Consumer\Service\Helper;

use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;

trait BodyTrait
{
    use FFITrait;
    use InteractionTrait;

    private function setBody(int $part, ?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->ffi->pactffi_with_body($this->getId(), $part, $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }
}
