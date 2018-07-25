<?php

namespace MessageProvider;

class ExampleMessageProvider
{
    /** @var array */
    private $metadata;

    /**
     * @var mixed
     */
    private $contents;

    public function __construct($metadata = [])
    {
        $this->metadata = $metadata;
    }

    

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    // perhaps build a json object, etc
    public function Publish($contents)
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;

        $obj->contents       = new \stdClass();
        $obj->contents->test = $contents;

        return \json_encode($obj);
    }

    // perhaps build a json object, etc
    public function PublishAnotherMessageType()
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;

        $obj->contents       = new \stdClass();
        $obj->contents->song = 'And the wind whispers Mary';

        return \json_encode($obj);
    }
}
