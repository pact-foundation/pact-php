<?php

namespace PhpPact\Standalone\Broker;

use Psr\Http\Message\UriInterface;

class BrokerConfig
{
    /** @var null|UriInterface */
    private ?UriInterface $brokerUri = null;

    /** @var null|string */
    private ?string $brokerToken     = null;

    /** @var null|string */
    private ?string $brokerUsername  = null;

    /** @var null|string */
    private ?string $brokerPassword  = null;

    /** @var bool */
    private bool $verbose            = false;

    /** @var null|string */
    private ?string $pacticipant     = null;

    /** @var null|string */
    private ?string $request         = null;
    /** @var null|string */
    private ?string $header          = null;
    /** @var null|string */
    private ?string $data            = null;
    /** @var null|string */
    private ?string $user            = null;
    /** @var null|string */
    private ?string $consumer        = null;
    /** @var null|string */
    private ?string $provider        = null;
    /** @var null|string */
    private ?string $description     = null;
    /** @var null|string */
    private ?string $uuid            = null;
    /** @var null|string */
    private ?string $version         = null;
    /** @var null|string */
    private ?string $branch          = null;
    /** @var null|string */
    private ?string $tag             = null;
    /** @var null|string */
    private ?string $name            = null;
    /** @var null|string */
    private ?string $repositoryUrl   = null;
    /** @var null|string */
    private ?string $url             = null;
    /** @var null|string */
    private ?string $consumerVersion = null;
    /** @var null|string */
    private ?string $pactLocations   = null;

    /**
     * @return null|string
     */
    public function getRepositoryUrl(): ?string
    {
        return $this->repositoryUrl;
    }

    /**
     * @param null|string $repositoryUrl
     *
     * @return $this
     */
    public function setRepositoryUrl(?string $repositoryUrl): self
    {
        $this->repositoryUrl = $repositoryUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     *
     * @return $this
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param null|string $version
     *
     * @return $this
     */
    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBranch(): ?string
    {
        return $this->branch;
    }

    /**
     * @param null|string $branch
     *
     * @return $this
     */
    public function setBranch(?string $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param null|string $tag
     *
     * @return $this
     */
    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRequest(): ?string
    {
        return $this->request;
    }

    /**
     * @param null|string $request
     *
     * @return $this
     */
    public function setRequest(?string $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHeader(): ?string
    {
        return $this->header;
    }

    /**
     * @param null|string $header
     *
     * @return $this
     */
    public function setHeader(?string $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param null|string $data
     *
     * @return $this
     */
    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param null|string $user
     *
     * @return $this
     */
    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getConsumer(): ?string
    {
        return $this->consumer;
    }

    /**
     * @param null|string $consumer
     *
     * @return $this
     */
    public function setConsumer(?string $consumer): self
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param null|string $provider
     *
     * @return $this
     */
    public function setProvider(?string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     *
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param null|string $uuid
     *
     * @return $this
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * @param bool $verbose
     *
     * @return $this
     */
    public function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;

        return $this;
    }

    /**
     * @return null|UriInterface
     */
    public function getBrokerUri(): ?UriInterface
    {
        return $this->brokerUri;
    }

    /**
     * @param null|UriInterface $brokerUri
     *
     * @return $this
     */
    public function setBrokerUri(?UriInterface $brokerUri): self
    {
        $this->brokerUri = $brokerUri;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrokerToken(): ?string
    {
        return $this->brokerToken;
    }

    /**
     * @param null|string $brokerToken
     *
     * @return $this
     */
    public function setBrokerToken(?string $brokerToken): self
    {
        $this->brokerToken = $brokerToken;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrokerUsername(): ?string
    {
        return $this->brokerUsername;
    }

    /**
     * @param null|string $brokerUsername
     *
     * @return $this
     */
    public function setBrokerUsername(?string $brokerUsername): self
    {
        $this->brokerUsername = $brokerUsername;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrokerPassword(): ?string
    {
        return $this->brokerPassword;
    }

    /**
     * @param null|string $brokerPassword
     *
     * @return $this
     */
    public function setBrokerPassword(?string $brokerPassword): self
    {
        $this->brokerPassword = $brokerPassword;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPacticipant(): ?string
    {
        return $this->pacticipant;
    }

    /**
     * @param null|string $pacticipant
     *
     * @return $this
     */
    public function setPacticipant(?string $pacticipant): self
    {
        $this->pacticipant = $pacticipant;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getConsumerVersion(): ?string
    {
        return $this->consumerVersion;
    }

    /**
     * @param null|string $consumerVersion
     *
     * @return $this
     */
    public function setConsumerVersion(?string $consumerVersion): self
    {
        $this->consumerVersion = $consumerVersion;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPactLocations(): ?string
    {
        return $this->pactLocations;
    }

    /**
     * @param string $locations
     *
     * @return $this
     */
    public function setPactLocations(string $locations): self
    {
        $this->pactLocations = $locations;

        return $this;
    }
}
