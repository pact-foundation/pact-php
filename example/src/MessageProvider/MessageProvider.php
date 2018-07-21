<?php

namespace MessageProvider;

class MessageProvider
{
    /** @var array */
    private $metadata;

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

        print \print_r($obj, true);

        return \json_encode($obj);
    }

    // perhaps build a json object, etc
    public function PublishAnotherMessageType()
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;

        $obj->contents       = new \stdClass();
        $obj->contents->song = 'And the wind whispers Mary';

        print \print_r($obj, true);

        return \json_encode($obj);
    }
}
