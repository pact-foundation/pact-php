<?php

class ProviderMessage
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
    public function Publish($content)
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;
        $obj->content  = $content;

        print \print_r($obj, true);

        return \json_encode($obj);
    }
}
