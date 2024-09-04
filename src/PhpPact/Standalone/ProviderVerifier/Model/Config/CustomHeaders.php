<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

class CustomHeaders implements CustomHeadersInterface
{
    /**
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * @param array<string, string> $headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }

        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
