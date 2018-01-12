<?php

namespace PhpPact;

use PhpPact\Mocks\MockHttpService\Validators;
use PhpPact\Models\Pacticipant;

class PactBuilder implements IPactBuilder
{
    /**
     * @var string
     */
    private $_consumerName;

    /**
     * @var string
     */
    private $_providerName;

    /**
     * @var \PhpPact\Mocks\MockHttpService\MockProviderService
     */
    private $_mockProviderService;

    /**
     * @var \PhpPact\PactConfig
     */
    private $_config;

    /**
     * PactBuilder constructor.
     *
     * @param null|\PhpPact\PactConfig $config
     */
    public function __construct($config = null)
    {
        if (!$config) {
            $this->_config = new PactConfig();
        } else {
            $this->_config = $config;
        }
    }

    /**
     * @return string
     */
    public function getConsumerName()
    {
        return $this->_consumerName;
    }

    /**
     * @param string $consumerName
     */
    public function setConsumerName($consumerName)
    {
        $this->_consumerName = $consumerName;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->_providerName;
    }

    /**
     * @param string $providerName
     */
    public function setProviderName($providerName)
    {
        $this->_providerName = $providerName;

        return $this;
    }

    /**
     * @return \PhpPact\PactConfig
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param \PhpPact\PactConfig $config
     */
    public function setConfig(PactConfig $config)
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * @param $providerName
     * @param PactConfig $config
     */
    public function setMockService($providerName, PactConfig $config)
    {
        $this->_mockProviderService = new Mocks\MockHttpService\MockProviderService($providerName, $config);

        return $this;
    }

    /**
     * @return Mocks\MockHttpService\MockProviderService
     */
    public function getMockService()
    {
        if (!$this->_mockProviderService) {
            throw new \RuntimeException('Mock provider service has not been initialized');
        }

        return $this->_mockProviderService;
    }

    /**
     * @param string $consumerName
     *
     * @return $this
     */
    public function serviceConsumer($consumerName)
    {
        if (!$consumerName) {
            throw new \RuntimeException('Please supply a non null or empty consumerName');
        }

        $this->_consumerName = $consumerName;

        return $this;
    }

    /**
     * @param string $providerName
     *
     * @return $this
     */
    public function hasPactWith($providerName)
    {
        if (!$providerName) {
            throw new \RuntimeException('Please supply a non null or empty providerName');
        }

        $this->_providerName = $providerName;

        if (!$this->_mockProviderService) {
            $this->setMockService($this->_providerName, $this->_config);
        }

        return $this;
    }

    /**
     * Validate and create the new pact file.
     *
     * @param bool|\PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile $pactFile
     */
    public function build($pactFile = false)
    {
        if (!$this->_mockProviderService) {
            throw new \RuntimeException('The Pact file could not be saved because the mock provider service is not initialised. Please initialise by calling the MockService() method.');
        }

        if (!$this->_consumerName) {
            throw new \RuntimeException('ConsumerName has not been set, please supply a consumer name using the serviceConsumer method.');
        }

        if (!$this->_providerName) {
            throw new \RuntimeException('ProviderName has not been set, please supply a provider name using the HasPactWith method.');
        }

        if ($pactFile && !($pactFile instanceof Mocks\MockHttpService\Models\ProviderServicePactFile)) {
            throw  new \RuntimeException('Pact file was passed in but not a valid object type');
        }

        // set if it is not passed in
        if (!$pactFile) {
            $pactFile = $this->_mockProviderService->getPactFile();
            $pactFile->setProvider(new Pacticipant($this->_providerName));
            $pactFile->setConsumer(new Pacticipant($this->_consumerName));
        }

        $pactValidator = new Validators\PactFileValidator();
        $pactValidator->validate($pactFile);

        $this->persistPactFile($pactFile);
    }

    /**
     * Actually persist the file
     */
    private function persistPactFile(Models\PactFile $pactFile)
    {
        $output   = \json_encode($pactFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $fileName = $this->_config->getPactDir() . '/' . $pactFile->getFileName();
        \file_put_contents($fileName, $output);
    }
}
