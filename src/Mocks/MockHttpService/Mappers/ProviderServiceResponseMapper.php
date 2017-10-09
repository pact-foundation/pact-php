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
        } elseif ($response instanceof \Psr\Http\Message\ResponseInterface) {
            $response = $this->HttpResponseConvert($response);
        } elseif ($response instanceof \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse) {
            return $response;
        }

        $headers = isset($response->headers)?$response->headers:array();
        $status = isset($response->status)?$response->status:null;

        $body = false;
        if (property_exists($response, "body")) {
            $contentType = $this->GetContentType($response);
            $body = $response->body;

            if (stripos($contentType, "application/json") !== false && !is_string($body)) {
                $body = \json_encode($body);
            }
        }

        $providerServiceResponse = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse($status, $headers, $body);
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

    /**
     * Mine the headers to pull out the content type
     *
     * @param $request
     * @return bool
     */
    private function GetContentType($response)
    {
        $contentTypeStr = "Content-Type";
        if (isset($response->headers) && isset($response->headers->$contentTypeStr)) {
            return $response->headers->$contentTypeStr;
        }

        return false;
    }
}
