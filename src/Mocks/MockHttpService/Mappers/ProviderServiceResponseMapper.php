<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use PhpPact\Mappers\MatchingRuleMapper;

class ProviderServiceResponseMapper implements \PhpPact\Mappers\IMapper
{
    /**
     * @param $response
     * @return ProviderServiceResponse
     */
    public function convert($response)
    {
        if (is_string($response)) {
            $response = \json_decode($response);
        } elseif ($response instanceof \Psr\Http\Message\ResponseInterface) {
            $response = $this->httpResponseConvert($response);
        } elseif ($response instanceof ProviderServiceResponse) {
            return $response;
        }

        if (isset($response->headers) && is_object($response->headers)) {
            $response->headers = (array) $response->headers;
        } else if (!isset($response->headers)) {
            $response->headers = array();
        }

        $status = isset($response->status)?$response->status:null;

        $body = false;
        if (property_exists($response, "body")) {
            $contentType = $this->getContentType($response);
            $body = $response->body;

            if (stripos($contentType, "application/json") !== false && !is_string($body)) {
                $body = \json_encode($body);
            }
        }

        $matchingRulesMapper = new MatchingRuleMapper();
        $matchingRules = $matchingRulesMapper->convert($response);

        $providerServiceResponse = new ProviderServiceResponse($status, $response->headers, $body, $matchingRules);

        return $providerServiceResponse;
    }

    private function httpResponseConvert(\Psr\Http\Message\ResponseInterface $response)
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
        $obj->headers = array();
        if (count($headerArray) > 0) {
            foreach ($headerArray as $header_key => $header_value) {
                if (!is_array($header_value)) {
                    throw new \Exception("This was an unexpected case based on the Windwalker implementation.   Make a unit test and pull request.");
                }
                if (count($header_value) > 1) {
                    throw new \Exception("This was an unexpected case based on the Windwalker implementation.   Make a unit test and pull request.");
                }

                $obj->headers[$header_key] = array_pop($header_value);
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
     * @param $response
     * @return bool
     */
    private function getContentType($response)
    {
        $contentTypeStr = "Content-Type";
        if (isset($response->headers) && isset($response->headers[$contentTypeStr])) {
            return $response->headers[$contentTypeStr];
        }

        return false;
    }
}
