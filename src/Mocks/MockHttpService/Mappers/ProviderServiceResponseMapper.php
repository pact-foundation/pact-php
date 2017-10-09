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
            $response = \json_decode($response, true);
        } else if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            $response = $this->HttpResponseConvert($response);
        } else if ($response instanceof \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse) {
            return $response;
        }

        $headers = $response['headers'] ?? array();
        $status = $response['status'] ?? null;

        $body = false;
        if (array_key_exists('body', $response)) {
            $contentType = $response['headers']['Content-Type'] ?? false;
            $body = $response['body'];

            if (stripos($contentType, "application/json") !== false && !is_string($body)) {
                $body = \json_encode($body);
            }
        }

        $providerServiceResponse = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse($status, $headers, $body);
        return $providerServiceResponse;
    }

    private function HttpResponseConvert(\Psr\Http\Message\ResponseInterface $response)
    {
        $obj = [];
        $headerArray = $response->getHeaders();

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
        $obj['headers'] = [];
        if (count($headerArray) > 0) {

            foreach ($headerArray as $header_key => $header_value) {
                if (!is_array($header_value)) {
                    throw new \Exception("This was an unexpected case based on the Windwalker implementation.   Make a unit test and pull request.");
                }
                if (count($header_value) > 1) {
                    throw new \Exception("This was an unexpected case based on the Windwalker implementation.   Make a unit test and pull request.");
                }

                $obj['headers'][$header_key] = array_pop($header_value);
            }
        }

        $obj['status'] = $response->getStatusCode();

        $body = (string)$response->getBody();
        if ($body) {
            $obj['body'] = $body;
        }

        return $obj;
    }
}
