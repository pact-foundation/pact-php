<?php

namespace PhpPact\Standalone\Broker;

use Psr\Http\Message\UriInterface;

class BrokerConfig
{
    /** @var null|UriInterface */
    private $brokerUri;

    /** @var null|string */
    private $brokerToken;

    /** @var null|string */
    private $brokerUsername;

    /** @var null|string */
    private $brokerPassword;

    /** @var bool */
    private $verbose = false;

    /** @var string|null */
    private $pacticipant;

    /** @var null|string */
    private $request;
    /** @var null|string */
    private $header;
    /** @var null|string */
    private $data;
    /** @var null|string */
    private $user;
    /** @var null|string */
    private $consumer;
    /** @var null|string */
    private $provider;
    /** @var null|string */
    private $description;
    /** @var null|string */
    private $uuid;
    /** @var null|string */
    private $version;
    /** @var null|string */
    private $tag;
    /** @var null|string */
    private $name;
    /** @var null|string */
    private $repositoryUrl;
    /** @var null|string */
    private $url;
    /** @var null|string */
    private $consumerVersion;
    /** @var null|string */
    private $pactLocations;

    /**
     * @return string|null
     */
    public function getRepositoryUrl(): ?string
    {
        return $this->repositoryUrl;
    }

    /**
     * @param string|null $repositoryUrl
     * @return BrokerConfig
     */
    public function setRepositoryUrl(?string $repositoryUrl): BrokerConfig
    {
        $this->repositoryUrl = $repositoryUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return BrokerConfig
     */
    public function setUrl(?string $url): BrokerConfig
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     * @return BrokerConfig
     */
    public function setVersion(?string $version): BrokerConfig
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string|null $tag
     * @return BrokerConfig
     */
    public function setTag(?string $tag): BrokerConfig
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return BrokerConfig
     */
    public function setName(?string $name): BrokerConfig
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequest(): ?string
    {
        return $this->request;
    }

    /**
     * @param string|null $request
     * @return BrokerConfig
     */
    public function setRequest(?string $request): BrokerConfig
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeader(): ?string
    {
        return $this->header;
    }

    /**
     * @param string|null $header
     * @return BrokerConfig
     */
    public function setHeader(?string $header): BrokerConfig
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string|null $data
     * @return BrokerConfig
     */
    public function setData(?string $data): BrokerConfig
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string|null $user
     * @return BrokerConfig
     */
    public function setUser(?string $user): BrokerConfig
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getConsumer(): ?string
    {
        return $this->consumer;
    }

    /**
     * @param string|null $consumer
     * @return BrokerConfig
     */
    public function setConsumer(?string $consumer): BrokerConfig
    {
        $this->consumer = $consumer;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string|null $provider
     * @return BrokerConfig
     */
    public function setProvider(?string $provider): BrokerConfig
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return BrokerConfig
     */
    public function setDescription(?string $description): BrokerConfig
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return BrokerConfig
     */
    public function setUuid(?string $uuid): BrokerConfig
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function isVerbose()
    {
        return $this->verbose;
    }

    /**
     * @return UriInterface|null
     */
    public function getBrokerUri(): ?UriInterface
    {
        return $this->brokerUri;
    }

    /**
     * @param UriInterface|null $brokerUri
     */
    public function setBrokerUri(?UriInterface $brokerUri): self
    {
        $this->brokerUri = $brokerUri;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrokerToken(): ?string
    {
        return $this->brokerToken;
    }

    /**
     * @param string|null $brokerToken
     */
    public function setBrokerToken(?string $brokerToken): self
    {
        $this->brokerToken = $brokerToken;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrokerUsername(): ?string
    {
        return $this->brokerUsername;
    }

    /**
     * @param string|null $brokerUsername
     */
    public function setBrokerUsername(?string $brokerUsername): self
    {
        $this->brokerUsername = $brokerUsername;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrokerPassword(): ?string
    {
        return $this->brokerPassword;
    }

    /**
     * @param string|null $brokerPassword
     */
    public function setBrokerPassword(?string $brokerPassword): self
    {
        $this->brokerPassword = $brokerPassword;
        return $this;
    }

    public function getPacticipant()
    {
        return $this->pacticipant;
    }

    /**
     * @param string|null $pacticipant
     */
    public function setPacticipant(?string $pacticipant): self
    {
        $this->pacticipant = $pacticipant;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getConsumerVersion(): ?string
    {
        return $this->consumerVersion;
    }

    /**
     * @param string|null $consumerVersion
     * @return BrokerConfig
     */
    public function setConsumerVersion(?string $consumerVersion): self
    {
        $this->consumerVersion = $consumerVersion;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPactLocations(): ?string
    {
        return $this->pactLocations;
    }

    /**
     * @param string $locations
     * @return BrokerConfig
     */
    public function setPactLocations(string $locations): self
    {
        $this->pactLocations = $locations;
        return $this;
    }
}
