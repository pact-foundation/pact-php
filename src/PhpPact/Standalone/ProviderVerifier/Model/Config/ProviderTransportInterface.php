<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface ProviderTransportInterface
{
    public const MESSAGE_PROTOCOL = 'message';
    public const SYNC_MESSAGE_PROTOCOL = 'sync-message';
    public const ASYNC_MESSAGE_PROTOCOL = 'async-message';
    public const HTTP_PROTOCOL = 'http';
    public const HTTPS_PROTOCOL = 'https';

    public function getProtocol(): ?string;

    public function setProtocol(?string $protocol): self;

    public function getScheme(): ?string;

    public function setScheme(?string $scheme): self;

    public function getPort(): ?int;

    public function setPort(?int $port): self;

    public function getPath(): ?string;

    public function setPath(?string $path): self;
}
