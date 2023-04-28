<?php

namespace PhpPact\Standalone\Broker;

use Psr\Http\Message\UriInterface;

class BrokerConfig
{
    private ?UriInterface $brokerUri = null;

    private ?string $brokerToken = null;

    private ?string $brokerUsername = null;

    private ?string $brokerPassword = null;

    private bool $verbose = false;

    private ?string $pacticipant = null;

    private ?string $request = null;

    private ?string $header = null;

    private ?string $data = null;

    private ?string $user = null;

    private ?string $consumer = null;

    private ?string $provider = null;

    private ?string $description = null;

    private ?string $uuid = null;

    private ?string $version = null;

    private ?string $branch = null;

    private ?string $tag = null;

    private ?string $name = null;

    private ?string $repositoryUrl = null;

    private ?string $url = null;

    private ?string $consumerVersion = null;

    private ?string $pactLocations = null;

    public function getRepositoryUrl(): ?string
    {
        return $this->repositoryUrl;
    }

    public function setRepositoryUrl(?string $repositoryUrl): self
    {
        $this->repositoryUrl = $repositoryUrl;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch(?string $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(?string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(?string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConsumer(): ?string
    {
        return $this->consumer;
    }

    public function setConsumer(?string $consumer): self
    {
        $this->consumer = $consumer;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    public function getBrokerUri(): ?UriInterface
    {
        return $this->brokerUri;
    }

    public function setBrokerUri(?UriInterface $brokerUri): self
    {
        $this->brokerUri = $brokerUri;

        return $this;
    }

    public function getBrokerToken(): ?string
    {
        return $this->brokerToken;
    }

    public function setBrokerToken(?string $brokerToken): self
    {
        $this->brokerToken = $brokerToken;

        return $this;
    }

    public function getBrokerUsername(): ?string
    {
        return $this->brokerUsername;
    }

    public function setBrokerUsername(?string $brokerUsername): self
    {
        $this->brokerUsername = $brokerUsername;

        return $this;
    }

    public function getBrokerPassword(): ?string
    {
        return $this->brokerPassword;
    }

    public function setBrokerPassword(?string $brokerPassword): self
    {
        $this->brokerPassword = $brokerPassword;

        return $this;
    }

    public function getPacticipant(): string
    {
        return $this->pacticipant;
    }

    public function setPacticipant(?string $pacticipant): self
    {
        $this->pacticipant = $pacticipant;

        return $this;
    }

    public function getConsumerVersion(): ?string
    {
        return $this->consumerVersion;
    }

    public function setConsumerVersion(?string $consumerVersion): self
    {
        $this->consumerVersion = $consumerVersion;

        return $this;
    }

    public function getPactLocations(): ?string
    {
        return $this->pactLocations;
    }

    public function setPactLocations(string $locations): self
    {
        $this->pactLocations = $locations;

        return $this;
    }
}
