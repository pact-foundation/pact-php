<?php

namespace PhpPact;

class PactVerifier implements IPactVerifier
{
    private $_httpClient;

    /**
     * @var PactVerifierConfig
     */
    private $_config;

    /**
     * @var Mocks\MockHttpService\IHttpRequestSender
     */
    private $_httpRequestSender;

    private $_consumerName;
    private $_providerName;
    private $_providerStates;
    private $_pactFileUri;
    private $_pactUriOptions;

    public function __construct($baseUri)
    {
        $this->_providerStates = new Models\ProviderStates();
        $this->_httpClient = new \Windwalker\Http\HttpClient();
        $this->_config = new PactVerifierConfig();
        $this->_config->setBaseUri($baseUri);
    }

    /**
     * @return PactVerifierConfig
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param PactVerifierConfig $config
     * @return PactVerifier
     */
    public function setConfig(PactVerifierConfig $config)
    {
        $this->_config = $config;
        return $this;
    }



    /**
     * Define a set up and/or tear down action for a specific state specified by the consumer.
     *
     * This is where you should set up test data, so that you can fulfil the contract outlined by a consumer.
     *
     * @param $providerState the name of the provider state as defined by the consumer interaction, which lives in the Pact file.
     * @param null $setUp A set up action that will be run before the interaction verify, if the provider has specified it in the interaction. If no action is required please use an empty lambda
     * @param null $tearDown A tear down action that will be run after the interaction verify, if the provider has specified it in the interaction. If no action is required please use an empty lambda
     * @return PactVerifier $this
     */
    public function providerState($providerState, $setUp = null, $tearDown = null)
    {
        if (!$providerState) {
            throw new \InvalidArgumentException("Please supply a non null or empty providerState");
        }

        $providerStateItem = new Models\ProviderState($providerState, $setUp, $tearDown);
        $this->_providerStates->add($providerStateItem);

        return $this;
    }

    public function serviceProvider($providerName, $httpClient = null, $httpRequestSender = null)
    {
        if (!$providerName) {
            throw new \InvalidArgumentException("Please supply a non null or empty providerName");
        }

        if ($this->_providerName) {
            throw new \InvalidArgumentException("ProviderName has already been supplied, please instantiate a new PactVerifier if you want to perform verification for a different provider");
        }

        if (!$httpRequestSender && !$httpClient) {
            throw new \InvalidArgumentException("Please supply either a non null httpRequestSender or httpClient");
        }

        $this->_providerName = $providerName;

        if ($httpRequestSender) {
            throw new \InvalidArgumentException('Overriding $httpRequestSender has not been implemented yet');
            // $this->_httpRequestSender = new CustomRequestSender(httpRequestSender);
        } else {
            $this->_httpRequestSender = new \PhpPact\Mocks\MockHttpService\HttpClientRequestSender($httpClient);
        }
        return $this;
    }

    public function honoursPactWith($consumerName)
    {
        if (!$consumerName) {
            throw new \InvalidArgumentException("Please supply a non null or empty consumerName");
        }

        if ($this->_consumerName) {
            throw new \InvalidArgumentException("ConsumerName has already been supplied, please instantiate a new PactVerifier if you want to perform verification for a different consumer");
        }

        $this->_consumerName = $consumerName;

        return $this;
    }

    public function pactUri($uri, $options = null)
    {
        if (!$uri) {
            throw new \InvalidArgumentException("Please supply a non null or empty consumerName");
        }

        if (!filter_var($uri, FILTER_VALIDATE_URL) && !file_exists($uri)) {
            throw new \InvalidArgumentException("URI does not exist on the file system or this is not a valid URI: " . $uri);
        }

        $this->_pactFileUri = $uri;
        $this->_pactUriOptions = $options;

        return $this;
    }

    public function verify($description = null, $providerState = null)
    {
        if (!$this->_httpRequestSender) {
            throw new \InvalidArgumentException("httpRequestSender has not been set, please supply a httpClient or httpRequestSenderFunc using the ServiceProvider method.");
        }

        if (!$this->_pactFileUri) {
            throw new \InvalidArgumentException("PactFileUri has not been set, please supply a uri using the PactUri method.");
        }

        $pactFileJson = file_get_contents($this->_pactFileUri);

        if ($pactFileJson === false) {
            throw new \RuntimeException("Pact file cannot be found: {$this->_pactFileUri}");
        }

        $jsonDecoded = \json_decode($pactFileJson);
        $mapper = new Mocks\MockHttpService\Mappers\ProviderServicePactMapper();

        $pactFile = $mapper->Convert($jsonDecoded);

        //Filter interactions
        if ($description != null) {
            $pactFile->filterInteractionsByDescription($description);
        }

        if ($providerState != null) {
            $pactFile->filterInteractionsByProviderState($providerState);
        }

        if (($description != null || $providerState != null) && count($pactFile->getInteractions()) == 0) {
            throw new \InvalidArgumentException("The specified description and/or providerState filter yielded no interactions.");
        }

        try {
            $reporter = new Reporters\Reporter($this->_config);
            $validator = new Mocks\MockHttpService\Validators\ProviderServiceValidator($this->_httpRequestSender, $reporter, $this->_config);
            $validator->validate($pactFile, $this->_providerStates);
        } catch (\Exception $e) {
            $this->_config->getLogger()->fatal("Unable to verify pact: " . $e->getMessage());
            throw $e;
        }
    }
}
