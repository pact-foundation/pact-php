<?php

namespace PhpPact\Standalone\Broker;

use Psr\Http\Message\UriInterface;

/**
 * Class BrokerConfig.
 */
class BrokerConfig
{
    private ?UriInterface $brokerUri = null;
    private ?string $brokerToken     = null;
    private ?string $brokerUsername  = null;
    private ?string $brokerPassword  = null;
    private bool $verbose            = false;
    private ?string $pacticipant     = null;
    private ?string $request         = null;
    private ?string $header          = null;
    private ?string $data            = null;
    private ?string $user            = null;
    private ?string $consumer        = null;
    private ?string $provider        = null;
    private ?string $description     = null;
    private ?string $uuid            = null;
    private ?string $version         = null;
    private ?string $branch          = null;
    private ?string $tag             = null;
    private ?string $name            = null;
    private ?string $repositoryUrl   = null;
    private ?string $url             = null;
    private ?string $consumerVersion = null;
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return null|UriInterface
     */
    public function getBrokerUri(): ?UriInterface
    {
        return $this->brokerUri;
    }

    /**
     * @param null|UriInterface $brokerUri
     *
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
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
     * @return BrokerConfig
     */
    public function setPactLocations(string $locations): self
    {
        $this->pactLocations = $locations;

        return $this;
    }
}
