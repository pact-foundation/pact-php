<?php

namespace PhpPact\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface
{
    /**
     * Get Request.
     *
     * @param array<string, mixed> $options
     */
    public function get(UriInterface $uri, array $options = []): ResponseInterface;

    /**
     * Put Request.
     *
     * @param array<string, mixed> $options
     */
    public function put(UriInterface $uri, array $options = []): ResponseInterface;

    /**
     * Post Request.
     *
     * @param array<string, mixed> $options
     */
    public function post(UriInterface $uri, array $options = []): ResponseInterface;

    /**
     * Delete Request.
     *
     * @param array<string, mixed> $options
     */
    public function delete(UriInterface $uri, array $options = []): ResponseInterface;
}
