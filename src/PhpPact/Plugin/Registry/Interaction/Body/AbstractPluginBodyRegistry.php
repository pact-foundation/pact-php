<?php

namespace PhpPact\Plugin\Registry\Interaction\Body;

use PhpPact\Consumer\Exception\BodyNotSupportedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Registry\Interaction\Body\BodyRegistryInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugin\Exception\PluginBodyNotAddedException;

abstract class AbstractPluginBodyRegistry implements BodyRegistryInterface
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function withBody(Text|Binary|Multipart $body): void
    {
        switch (true) {
            case $body instanceof Binary:
                throw new BodyNotSupportedException('Plugin does not support binary body');

            case $body instanceof Text:
                json_decode($body->getContents());
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new BodyNotSupportedException('Plugin does not support non-json body contents');
                }
                $error = $this->client->call('pactffi_interaction_contents', $this->getInteractionId(), $this->getPart(), $body->getContentType(), $body->getContents());
                if ($error) {
                    throw new PluginBodyNotAddedException($error);
                }
                break;

            case $body instanceof Multipart:
                throw new BodyNotSupportedException('Plugin does not support multipart body');
        };
    }

    abstract protected function getInteractionId(): int;

    abstract protected function getPart(): int;
}
