<?php

namespace PhpPact\Mocks\MockHttpService;

use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper;
use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper;
use PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer;
use PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceInteraction;
use PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper;
use PhpPact\Mocks\MockHttpService\Mappers\HttpResponseMessageMapper;
use PhpPact\Mocks\MockHttpService\MockProviderHost;
use PhpPact\Mocks\MockHttpService\Models\IHttpMessage;
use PhpPact\Mocks\MockHttpService\Models\HttpVerb;

class MockProviderService implements IMockProviderService
{
    private $_providerState;
    private $_description;

    /**
     * @var MockProviderHost
     */
    private $_host;

    /**
     * @var \Windwalker\Http\HttpClient
     */
    private $_httpClient;

    /**
     * @var ProviderServiceRequest
     */
    private $_request;

    /**
     * @var ProviderServiceResponse
     */
    private $_response;

    /**
     * @var \PhpPact\PactConfig
     */
    private $_config;

    /**
     * @var ProviderServicePactFile
     */
    private $_pactFile;

    public function __construct($providerName, \PhpPact\PactConfig $config)
    {
        $this->_config = $config;
        $this->_httpClient = new \Windwalker\Http\HttpClient();
        $this->_host = new MockProviderHost();

        $pactFile = new ProviderServicePactFile();
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
     * @return MockProviderHost
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * @return ProviderServicePactFile
     */
    public function getPactFile()
    {
        return $this->_pactFile;
    }

    /**
     * @param ProviderServicePactFile $pactFile
     */
    public function setPactFile(&$pactFile)
    {
        if (!($pactFile instanceof ProviderServicePactFile)) {
            throw new \RuntimeException("Expected pactFile");
        }

        $this->_pactFile = $pactFile;
    }


    /**
     * @param ProviderServiceInteraction $interactions
     */
    public function addInteractionToPact(ProviderServiceInteraction $interaction)
    {
        $this->_pactFile->addInteraction($interaction);
    }

    /**
     * @param string $providerState
     * @return $this
     */
    public function given($providerState)
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
    public function uponReceiving($description)
    {
        if (!$description) {
            throw new \InvalidArgumentException("Please supply a non null or empty description");
        }

        $this->_description = $description;

        return $this;
    }

    /**
     * @param ProviderServiceRequest $request
     * @return $this
     */
    public function with($request)
    {
        if ($request == null || !($request instanceof ProviderServiceRequest)) {
            throw new \InvalidArgumentException("Please supply a non null request");
        }

        $method = $request->getMethod();
        if ((string)$method == (string)HttpVerb::NOTSET) {
            throw new \InvalidArgumentException("Please supply a request Method");
        }

        if (!$request->getPath()) {
            throw new \InvalidArgumentException("Please supply a request Path");
        }

        if (!$this->isContentTypeSpecifiedForBody($request)) {
            throw new \InvalidArgumentException("Please supply a Content-Type request header");
        }

        $this->_request = $request;

        return $this;
    }

    /**
     * @param ProviderServiceResponse $response
     * @return $this
     */
    public function willRespondWith($response)
    {
        if ($response == null || !($response instanceof ProviderServiceResponse)) {
            throw new \InvalidArgumentException("Please supply a non null response");
        }

        if ($response->getStatus() <= 0) {
            throw new \InvalidArgumentException("Please supply a response Status");
        }

        if (!$this->isContentTypeSpecifiedForBody($response)) {
            throw new \InvalidArgumentException("Please supply a Content-Type response header");
        }

        $this->_response = $response;
        $this->registerInteraction();
        $this->ClearTransientState();
    }


    public function verifyInteractions()
    {
        $requestMapper = new ProviderServiceRequestMapper();

        $responseMapper = new ProviderServiceResponseMapper();
        $responseComparer = new ProviderServiceResponseComparer();

        $comparisonResults = new \PhpPact\Comparers\ComparisonResult();

        $this->_pactFile->setLogger($this->_config->getLogger());

        // cycle through all the requests we sent
        foreach ($this->_host->getRequestAndResponsePairs() as $pair) {
            if (!isset($pair[MockProviderHost::REQUEST_KEY])) {
                throw new \RuntimeException("Request was not set: " . print_r($pair, true));
            }

            if (!isset($pair[MockProviderHost::RESPONSE_KEY])) {
                throw new \RuntimeException("Response was not set: " . print_r($pair, true));
            }

            $httpRequest = $pair[MockProviderHost::REQUEST_KEY];
            $httpResponse = $pair[MockProviderHost::RESPONSE_KEY];

            $request = $requestMapper->convert($httpRequest);
            $response = $responseMapper->convert($httpResponse);

            // foreach request, check that request is in our list of interactions
            $matchingInteraction = $this->_pactFile->findInteractionByProviderServiceRequest($request);

            // given that we got a response, does it look like the expected interactions response
            $matchingResponse = $matchingInteraction->getResponse();
            $result = $responseComparer->compare($matchingResponse, $response);

            $comparisonResults->addChildResult($result);
        }

        if ($comparisonResults->hasFailure()) {
            throw new \PhpPact\PactFailureException("See test output or logs for failure details.");
        }

        return $comparisonResults;
    }

    /**
     * Encapsulate the initialization of the mock service
     */
    public function start()
    {
        $this->_host = new MockProviderHost();
    }

    /**
     * Clear previous hosts and reset states
     */
    public function stop()
    {
        $this->clearAllState();
        $this->_host = null;
    }

    /**
     * Clear the interactions
     */
    public function clearInteractions()
    {
        $this->_pactFile->setInteractions(array());
    }

    /**
     * @param ProviderServiceRequest $providerServiceRequest
     * @param string $baseUri
     * @return ProviderServiceResponse
     * @throws \PhpPact\PactFailureException
     */
    public function sendMockRequest(ProviderServiceRequest $providerServiceRequest, $baseUri)
    {
        if ($this->_host == null) {
            throw new \RuntimeException("Unable to perform operation because the mock provider service is not running.");
        }

        $responseContent = '';

        $httpRequestMapper = new HttpRequestMessageMapper();
        $httpResponseMapper = new ProviderServiceResponseMapper();

        $httpRequest = $httpRequestMapper->convert($providerServiceRequest, $baseUri);
        $httpResponse = $this->_host->handle($httpRequest);
        $providerResponse = $httpResponseMapper->convert($httpResponse);

        $responseStatusCode = $providerResponse->getStatus();

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
    private function registerInteraction()
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

        $interaction = new ProviderServiceInteraction();
        $interaction->setProviderState($this->_providerState);
        $interaction->setDescription($this->_description);
        $interaction->setRequest($this->_request);
        $interaction->setResponse($this->_response);

        $this->addMockToServer($interaction);

        // do we actually want to set the iteractions to the pact.  I think we want to do this after we verify or build.
        $this->addInteractionToPact($interaction);
    }

    /**
     * @param ProviderServiceInteraction $interaction
     */
    public function addMockToServer(ProviderServiceInteraction $interaction)
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
        $mapper = new HttpResponseMessageMapper();
        $httpResponse = $mapper->convert($interaction->getResponse());
        $server = $server->return($i = $httpResponse);
        $server = $server->end();

        $this->_host = $server;
    }

    /**
     * Clear everything
     */
    private function clearAllState()
    {
        $this->clearTransientState();
        $this->clearInteractions();
    }

    /**
     * Clear the responses and requests.  Keep the interactions.
     */
    private function clearTransientState()
    {
        $this->_request = null;
        $this->_response = null;
        $this->_providerState = null;
        $this->_description = null;
    }

    /**
     * @param IHttpMessage $message
     * @return bool
     */
    private function isContentTypeSpecifiedForBody(IHttpMessage $message)
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
