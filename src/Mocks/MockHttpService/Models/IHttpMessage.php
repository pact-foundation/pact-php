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
}