<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;

class HttpRequestMessageMapper
{

    /**
     * Convert interaction objects into http requests
     *
     * This should likely not be in the generic http request message class
     *
     * @param ProviderServiceRequest $request
     * @param string $baseUri
     * @return \Windwalker\Http\Request\Request
     */
    public function convert(ProviderServiceRequest $request, $baseUri)
    {
        if (substr($baseUri, -1) == '/') {
            $baseUri = substr($baseUri, 0, strlen($baseUri) - 1);
        }

        $httpRequest = new \Windwalker\Http\Request\Request();

        $uri = (new \Windwalker\Uri\PsrUri($baseUri))
            ->withPath($request->getPath());

        // Windwalker requires Host to be set
        $uri = $this->augmentUriWithHostHeader($uri, $request->getHeaders());
        
        if ($request->getQuery()) {
            $uri = $uri->withQuery($request->getQuery());
        }

        // loop through each header, check for lowercase, if no duplicates, add the first one
        if (count($request->getHeaders()) > 0) {
            foreach ($request->getHeaders() as $header_key => $header_value) {
                $normalizedHeaderKey = strtolower($header_key);
                if (!$httpRequest->getHeader($header_key) && !$httpRequest->getHeader($normalizedHeaderKey) ) {
                    $httpRequest = $httpRequest->withAddedHeader($header_key, $header_value);
                }
            }
        }
        
        $httpRequest = $httpRequest->withUri($uri)
                            ->withMethod($request->getMethod());
        

        if ($request->getBody()) {
            $body = $request->getBody();
            if (!is_string($body)) {
                $body = \json_encode($body);
            }

            $httpRequest->getBody()->write($body);
        }

        return $httpRequest;
    }
    
    /**
     * Windwalker requires Host header to be set
     * 
     * @param \Windwalker\Uri\PsrUri $uri
     * @param array $headers
     */
    function augmentUriWithHostHeader(\Windwalker\Uri\PsrUri $uri, $headers) {
        if ($headers) {
            if (isset($headers['Host'])) {
                $uri = $uri->withHost($headers['Host']);  
            } else if (isset($headers['host'])) {
                $uri = $uri->withHost($headers['host']);
            }
        }
        
        return $uri;
    }
}
