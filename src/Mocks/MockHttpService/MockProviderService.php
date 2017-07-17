<?php

namespace PhpPact\Mocks\MockHttpService;


class MockProviderService implements IMockProviderService
{
    private $_providerState;
    private $_description;

    /**
     * @var \PhpPact\Mocks\MockHttpService\MockProviderHost
     */
    private $_host;

    /**
     * @var \Windwalker\Http\HttpClient
     */
    private $_httpClient;

    /**
     * @var \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     */
    private $_request; //ProviderServiceRequest

    /**
     * @var \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     */
    private $_response;

    /**
     * @var \PhpPact\PactConfig
     */
    private $_config;

    /**
     * @var \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile
     */
    private $_pactFile;

    public function __construct($providerName, \PhpPact\PactConfig $config)
    {
        $this->_config = $config;
        $this->_httpClient = new \Windwalker\Http\HttpClient();
        $this->_host = new \PhpPact\Mocks\MockHttpService\MockProviderHost();

        $pactFile = new \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile();
        $this->setPactFile($pactFile);
    }

    /**
     * Get the URI from the config
     *
     * @return mixed
     */
    public function getUri()
    {
        return $this->_config->getBaseUri();
    }

    /**
     * Get the URN (URI sans protocol and port) from the config
     *
     * @return mixed
     */
    public function getUrn()
    {
        return $this->_config->getBaseUrn();
    }

    /**
     * Get the port from the config
     *
     * @return mixed
     */
    public function getPort()
    {
        return $this->_config->getPort();
    }

    /**
     * @return \PhpPact\Mocks\MockHttpService\MockProviderHost(
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * @return \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile
     */
    public function getPactFile()
    {
        return $this->_pactFile;
    }

    /**
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile $pactFile
     */
    public function setPactFile(&$pactFile)
    {
        if (!($pactFile instanceof \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile)) {
            throw new \RuntimeException("Expected pactFile");
        }

        $this->_pactFile = $pactFile;
    }


    /**
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction $interactions
     */
    public function AddInteractionToPact(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction $interaction)
    {
        $this->_pactFile->AddInteraction($interaction);
    }

    /**
     * @param string $providerState
     * @return $this
     */
    public function Given($providerState)
    {
        if (!$providerState) {
            throw new \InvalidArgumentException("Please supply a non null or empty providerState");
        }

        $this->_providerState = $providerState;

        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function UponReceiving($description)
    {
        if (!$description) {
            throw new \InvalidArgumentException("Please supply a non null or empty description");
        }

        $this->_description = $description;

        return $this;
    }

    /**
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $request
     * @return $this
     */
    public function With($request)
    {
        if ($request == null || !($request instanceof \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest)) {
            throw new \InvalidArgumentException("Please supply a non null request");
        }

        $method = $request->getMethod();
        if ((string)$method == (string)\PhpPact\Mocks\MockHttpService\Models\HttpVerb::NOTSET) {
            throw new \InvalidArgumentException("Please supply a request Method");
        }

        if (!$request->getPath()) {
            throw new \InvalidArgumentException("Please supply a request Path");
        }

        if (!$this->IsContentTypeSpecifiedForBody($request)) {
            throw new \InvalidArgumentException("Please supply a Content-Type request header");
        }

        $this->_request = $request;

        return $this;
    }

    /**
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse $response
     * @return $this
     */
    public function WillRespondWith($response)
    {
        if ($response == null || !($response instanceof \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse)) {
            throw new \InvalidArgumentException("Please supply a non null response");
        }

        if ($response->getStatus() <= 0) {
            throw new \InvalidArgumentException("Please supply a response Status");
        }

        if (!$this->IsContentTypeSpecifiedForBody($response)) {
            throw new \InvalidArgumentException("Please supply a Content-Type response header");
        }

        $this->_response = $response;
        $this->RegisterInteraction();
        $this->ClearTransientState();
    }


    public function VerifyInteractions()
    {
        $requestMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper();

        $responseMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper();
        $responseComparer = new \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer();

        $comparisonResults = new \PhpPact\Comparers\ComparisonResult();

        $this->_pactFile->setLogger($this->_config->getLogger());

        // cycle through all the requests we sent
        foreach ($this->_host->getRequestAndResponsePairs() as $pair) {

            if (!isset($pair[\PhpPact\Mocks\MockHttpService\MockProviderHost::REQUEST_KEY])) {
                throw new \RuntimeException("Request was not set: " . print_r($pair, true));
            }

            if (!isset($pair[\PhpPact\Mocks\MockHttpService\MockProviderHost::RESPONSE_KEY])) {
                throw new \RuntimeException("Response was not set: " . print_r($pair, true));
            }

            $httpRequest = $pair[\PhpPact\Mocks\MockHttpService\MockProviderHost::REQUEST_KEY];
            $httpResponse = $pair[\PhpPact\Mocks\MockHttpService\MockProviderHost::RESPONSE_KEY];

            $request = $requestMapper->Convert($httpRequest);
            $response = $responseMapper->Convert($httpResponse);

            // foreach request, check that request is in our list of interactions
            $matchingInteraction = $this->_pactFile->FindInteractionByProviderServiceRequest($request);

            // given that we got a response, does it look like the expected interactions response
            $matchingResponse = $matchingInteraction->getResponse();
            $results = $responseComparer->Compare($matchingResponse, $response);

            $comparisonResults->AddChildResult($result);
        }

        if ($comparisonResults->HasFailure()) {
            throw new \PhpPact\PactFailureException("See test output or logs for failure details.");
        }

        return $comparisonResults;
    }

    /**
     * Encapsulate the initialization of the mock service
     */
    public function Start()
    {
        $this->_host = new \PhpPact\Mocks\MockHttpService\MockProviderHost();
    }

    /**
     * Clear previous hosts and reset states
     */
    public function Stop()
    {
        $this->ClearAllState();
        $this->_host = null;
    }

    /**
     * Clear the interactions
     */
    public function ClearInteractions()
    {
        $this->_pactFile->setInteractions(array());
    }

    /**
     * @param \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $providerServiceRequest
     * @param string $baseUri
     * @return \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     * @throws \PhpPact\PactFailureException
     */
    public function SendMockRequest(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest $providerServiceRequest, $baseUri)
    {
        if ($this->_host == null) {
            throw new \RuntimeException("Unable to perform operation because the mock provider service is not running.");
        }

        $responseContent = '';

        $httpRequestMapper = new \PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper();
        $httpResponseMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper();

        $httpRequest = $httpRequestMapper->Convert($providerServiceRequest, $baseUri);
        $httpResponse = $this->_host->handle($httpRequest);
        $providerResponse = $httpResponseMapper->Convert($httpResponse);

        $responseStatusCode = $providerResponse->getStatus();

        /*
        // @todo need to add content
        if ($providerResponse->getContent()) {
            $responseContent = (string)$providerResponse->getContent();
        }
        */

        unset($httpRequest);
        unset($httpResponse);

        if ($responseStatusCode != 200 /* HttpStatusCode::OK */) {
            throw new \PhpPact\PactFailureException($responseContent);
        }

        return $providerResponse;
    }


    /**
     * Register this interaction to the internal array
     * Wipe the other temp variables.
     *
     */
    private function RegisterInteraction()
    {
        if (!$this->_description) {
            throw new \RuntimeException("description has not been set, please supply using the UponReceiving method.");
        }

        if (!$this->_request) {
            throw new \RuntimeException("request has not been set, please supply using the With method.");
        }

        if (!$this->_response) {
            throw new \RuntimeException("response has not been set, please supply using the WillRespondWith method.");
        }

        $interaction = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction();
        $interaction->setProviderState($this->_providerState);
        $interaction->setDescription($this->_description);
        $interaction->setRequest($this->_request);
        $interaction->setResponse($this->_response);

        $this->AddMockToServer($interaction);

        // do we actually want to set the iteractions to the pact.  I think we want to do this after we verify or build.
        $this->AddInteractionToPact($interaction);
    }

    /**
     * @param Models\ProviderServiceInteraction $interaction
     */
    public function AddMockToServer(\PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction $interaction)
    {
        if ($this->_host == null) {
            throw new \RuntimeException("Host has not been set.");
        }

        $server = $this->_host;


        /**
         * Needed to handle pathing: http://localhost/test.php in the \Jfalque\HttpMock\Server() library
         *
         * The (string) $uri = (new \Windwalker\Uri\PsrUri($url))->withPath("/test.php") will return http://localhost/test.php
         * If this is not a regex, \Jfalque\HttpMock\Server() and the URI predict analysis will compare one string with
         * the path appended and another without the path appended.
         *
         * http://localhost/test.php != http://localhost/
         *
         */
        $pattern = '/' . str_replace("/", "\/", $this->getUri()) . "*/";
        $server = $server->whenUri($pattern, true)
            ->andWhenMethod($interaction->getRequest()->getMethod())
            ->andWhenPath($interaction->getRequest()->getPath());


        if (count($interaction->getRequest()->getHeaders()) > 0) {
            $server = $server->andWhenHeaders($interaction->getRequest()->getHeaders());
        }

        if ($interaction->getRequest()->getQuery()) {
            $server = $server->andWhenQuery($interaction->getRequest()->getQuery());
        }

        if ($interaction->getRequest()->getBody()) {
            $server = $server->andWhenBody($interaction->getRequest()->getBody());
        }

        // work through a conversion to $mapper
        $mapper = new \PhpPact\Mocks\MockHttpService\Mappers\HttpResponseMessageMapper();
        $httpResponse = $mapper->Convert($interaction->getResponse());
        $server = $server->return($i = $httpResponse);
        $server = $server->end();

        $this->_host = $server;
    }

    /**
     * Clear everything
     */
    private function ClearAllState()
    {
        $this->ClearTransientState();
        $this->ClearInteractions();
    }

    /**
     * Clear the responses and requests.  Keep the interactions.
     */
    private function ClearTransientState()
    {
        $this->_request = null;
        $this->_response = null;
        $this->_providerState = null;
        $this->_description = null;
    }

    /**
     * @param Models\IHttpMessage $message
     * @return bool
     */
    private function IsContentTypeSpecifiedForBody(\PhpPact\Mocks\MockHttpService\Models\IHttpMessage $message)
    {
        //No content-type required if there is no body
        if (!$message->getBody()) {
            return true;
        }

        $headers = null;
        if (count($message->getHeaders()) > 0) {
            $headers = $message->getHeaders();
        }

        return $headers != null && isset($headers["Content-Type"]);
    }

}
