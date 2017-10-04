<?php

namespace PhpPact\Mocks\MockHttpService\Models;

interface IHttpMessage
{
    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     * @return mixed
     */
    public function setBody($body);

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param array $headers
     * @return mixed
     */
    public function setHeaders($headers);

    /**
     * Return the header value for Content-Type
     *
     * False is returned if not set
     *
     * @return mixed|bool
     */
    public function getContentType();
}