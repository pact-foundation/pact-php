<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;

class HttpResponseMessageMapper
{
    /**
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse $response
     *
     * @return \Windwalker\Http\Response\Response
     */
    public function convert(ProviderServiceResponse $response)
    {
        $httpResponse = new \Windwalker\Http\Response\Response();

        $httpResponse = $httpResponse->withStatus($response->getStatus());

        foreach ($response->getHeaders() as $header_key => $header_value) {
            $httpResponse = $httpResponse->withAddedHeader($header_key, $header_value);
        }

        if ($response->getBody()) {
            $httpResponse->getBody()->write($response->getBody());
        }

        return $httpResponse;
    }
}
