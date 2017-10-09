<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

use Windwalker\Http\Request\Request;

class HttpRequestMessageMapper
{

    /**
     * Convert interaction objects into http requests
     *
     * This should likely not be in the generic http request message class
     *
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $request
     * @param string $baseUri
     * @return \Windwalker\Http\Request\Request
     */
    public function Convert(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $request, $baseUri)
    {
        if (substr($baseUri, -1) == '/') {
            $baseUri = substr($baseUri, 0, strlen($baseUri) - 1);
        }

        $httpRequest = new \Windwalker\Http\Request\Request();

        $uri = (new \Windwalker\Uri\PsrUri($baseUri))
            ->withPath($request->getPath());

        if ($request->getQuery()) {
            $uri = $uri->withQuery($request->getQuery());
        }

        $httpRequest = $httpRequest->withUri($uri)
            ->withMethod($request->getMethod());

        foreach ($request->getHeaders() as $header_key => $header_value) {
            $httpRequest = $httpRequest->withAddedHeader($header_key, $header_value);
        }

        if ($request->getBody()) {
            $body = $request->getBody();
            if (!is_string($body)) {
                $body = \json_encode($body);
            }

            $httpRequest->getBody()->write($body);
        }

        return $httpRequest;
    }
}
