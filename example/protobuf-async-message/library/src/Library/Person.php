<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: example/protobuf-async-message/library/proto/say_hello.proto

namespace Library;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>library.Person</code>
 */
class Person extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string id = 1;</code>
     */
    protected $id = '';
    /**
     * Generated from protobuf field <code>.library.Name name = 2;</code>
     */
    protected $name = null;
    /**
     * Generated from protobuf field <code>map<string, int32> children = 3;</code>
     */
    private $children;
    /**
     * Generated from protobuf field <code>repeated string hobbies = 4;</code>
     */
    private $hobbies;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $id
     *     @type \Library\Name $name
     *     @type array|\Google\Protobuf\Internal\MapField $children
     *     @type array<string>|\Google\Protobuf\Internal\RepeatedField $hobbies
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Example\ProtobufAsyncMessage\Library\Proto\SayHello::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string id = 1;</code>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Generated from protobuf field <code>string id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setId($var)
    {
        GPBUtil::checkString($var, True);
        $this->id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.library.Name name = 2;</code>
     * @return \Library\Name|null
     */
    public function getName()
    {
        return $this->name;
    }

    public function hasName()
    {
        return isset($this->name);
    }

    public function clearName()
    {
        unset($this->name);
    }

    /**
     * Generated from protobuf field <code>.library.Name name = 2;</code>
     * @param \Library\Name $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkMessage($var, \Library\Name::class);
        $this->name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>map<string, int32> children = 3;</code>
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Generated from protobuf field <code>map<string, int32> children = 3;</code>
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setChildren($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::INT32);
        $this->children = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string hobbies = 4;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }

    /**
     * Generated from protobuf field <code>repeated string hobbies = 4;</code>
     * @param array<string>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setHobbies($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->hobbies = $arr;

        return $this;
    }

}

