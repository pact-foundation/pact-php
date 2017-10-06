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
    public function getHeaders() : array;

    /**
     * @param array $headers
     * @return mixed
     */
    public function setHeaders(array $headers);

    /**
     * Return the header value for Content-Type
     *
     * False is returned if not set
     *
     * @return mixed|bool
     */
    public function getContentType();
}
