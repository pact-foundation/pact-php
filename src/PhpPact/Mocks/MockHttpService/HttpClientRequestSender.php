<?php

namespace PhpPact\Mocks\MockHttpService;

class HttpClientRequestSender implements HttpRequestSenderInterface
{
    /**
     * @var \Windwalker\Http\HttpClient
     */
    private $_httpClient; //HttpClient

    /**
     * @var \PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper
     */
    private $_httpRequestMessageMapper;

    /**
     * @var \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper
     */
    private $_providerServiceResponseMapper;

    public function __construct($httpClient)
    {
        $this->_httpClient                    = $httpClient;
        $this->_httpRequestMessageMapper      = new \PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper();
        $this->_providerServiceResponseMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper();
    }

    /**
     * @param $request \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param string $baseUri - used to append to the path
     *
     * @return \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     */
    public function Send($request, $baseUri)
    {
        $httpRequest  = $this->_httpRequestMessageMapper->convert($request, $baseUri);
        $httpResponse = $this->_httpClient->sendRequest($httpRequest);
        $response     = $this->_providerServiceResponseMapper->convert($httpResponse);

        unset($httpRequest);
        unset($httpResponse);

        return $response;
    }
}
