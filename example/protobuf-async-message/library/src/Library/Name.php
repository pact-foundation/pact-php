<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: example/protobuf-async-message/library/proto/say_hello.proto

namespace Library;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>library.Name</code>
 */
class Name extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string given = 1;</code>
     */
    protected $given = '';
    /**
     * Generated from protobuf field <code>string surname = 2;</code>
     */
    protected $surname = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $given
     *     @type string $surname
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Example\ProtobufAsyncMessage\Library\Proto\SayHello::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string given = 1;</code>
     * @return string
     */
    public function getGiven()
    {
        return $this->given;
    }

    /**
     * Generated from protobuf field <code>string given = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setGiven($var)
    {
        GPBUtil::checkString($var, True);
        $this->given = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string surname = 2;</code>
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Generated from protobuf field <code>string surname = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSurname($var)
    {
        GPBUtil::checkString($var, True);
        $this->surname = $var;

        return $this;
    }

}

