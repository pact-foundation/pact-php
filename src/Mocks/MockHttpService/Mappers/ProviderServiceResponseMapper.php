<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

class ProviderServiceResponseMapper implements \PhpPact\Mappers\IMapper
{
    /**
     * @param $response
     * @return \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     */
    public function Convert($response)
    {
        if (is_string($response)) {
            $response = \json_decode($response);
        } else if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            $response = $this->HttpResponseConvert($response);
        } else if ($response instanceof \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse) {
            return $response;
        }

        $this->checkExistence($response, "status");
        $this->checkExistence($response, "headers");

        $body = false;
        if (property_exists($response, "body")) {
            $body = $response->body;

            $contentTypeStr = "Content-Type";
            if (isset($response->headers->$contentTypeStr)
                && stripos($response->headers->$contentTypeStr, "application/json") !== false
                && !is_string($body)
            ) {
                $body = \json_encode($body);
            }
        }

        $providerServiceResponse = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse($response->status, $response->headers, $body);
        return $providerServiceResponse;
    }

    private function HttpResponseConvert(\Psr\Http\Message\ResponseInterface $response)
    {
        $obj = new \stdClass();
        $headerArray = (array)$response->getHeaders();

        /*
         * Expected format
         [headers] => Array
        (
            [Host] => Array
                (
                    [0] => localhost:1239
                )

            [Date] => Array
                (
                    [0] => Fri, 30 Jun 2017 21:50:19 +0000
                )
        */
        $obj->headers = new \stdClass();
        if (count($headerArray) > 0) {

            foreach ($headerArray as $header_key => $header_value) {
                if (!is_array($header_value)) {
                    throw new \Exception("This was an unexpected case based on the Windwalker implementation.   Make a unit test and pull request.");
                }
                if (count($header_value) > 1) {
                    throw new \Exception("This was an unexpected case based on the Windwalker implementation.   Make a unit test and pull request.");
                }

                $obj->headers->$header_key = array_pop($header_value);
            }
        }

        $obj->status = $response->getStatusCode();

        $body = (string)$response->getBody();
        if ($body) {
            $obj->body = $body;
        }

        return $obj;
    }

    private function checkExistence($obj, $attr)
    {
        if (!isset($obj->$attr)) {
            throw new \InvalidArgumentException("$attr was not set");
        }
    }
}