<?php

namespace PhpPact\Mocks\MockHttpService\Models;

class HttpVerb
{
    const NOTSET = 0; // only one who had a number in C#
    const GET    = 'GET';
    const POST   = 'POST';
    const PUT    = 'PUT';
    const DELETE = 'DELETE';
    const HEAD   = 'HEAD';
    const PATCH  = 'PATCH';

    private $methodTypes;

    public function __construct()
    {
        $this->methodTypes           = [];
        $this->methodTypes['NOTSET'] = self::NOTSET;
        $this->methodTypes['GET']    = self::GET;
        $this->methodTypes['POST']   = self::POST;
        $this->methodTypes['PUT']    = self::PUT;
        $this->methodTypes['DELETE'] = self::DELETE;
        $this->methodTypes['HEAD']   = self::HEAD;
        $this->methodTypes['PATCH']  = self::PATCH;
    }

    public function Enum($method)
    {
        $method = \strtoupper($method);
        if (isset($this->methodTypes[$method])) {
            return $this->methodTypes[$method];
        }

        return self::NOTSET;
    }
}
