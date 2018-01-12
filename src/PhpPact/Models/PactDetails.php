<?php

namespace PhpPact\Models;

class PactDetails implements \JsonSerializable
{

    /**
     * @var Pacticipant
     */
    private $_provider;

    /**
     * @var Pacticipant
     */
    private $_consumer;

    /**
     * @var \Logger $_logger
     */
    protected $_logger;

    /**
     * @param \Logger $logger
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    public function jsonSerialize()
    {
        // this _should_ cascade to child classes
        $obj = new \stdClass();
        $obj->provider = $this->_provider;
        $obj->consumer = $this->_consumer;

        return $obj;
    }

    /**
     * @return Pacticipant
     */
    public function getProvider()
    {
        return $this->_provider;
    }

    /**
     * @param Pacticipant $provider
     */
    public function setProvider($provider)
    {
        $this->_provider = $provider;
    }

    /**
     * @return Pacticipant
     */
    public function getConsumer()
    {
        return $this->_consumer;
    }

    /**
     * @param Pacticipant $consumer
     */
    public function setConsumer($consumer)
    {
        $this->_consumer = $consumer;
    }

    public function GeneratePactFileName()
    {
        $fileName = sprintf(
            "%s-%s.json",
            $this->_consumer != null ? $this->_consumer->Name : "",
            $this->_provider != null ? $this->_provider->Name : ""
        );
        return \PhpPact\Extensions\StringExtensions::ToLowerSnakeCase($fileName);
    }
}
