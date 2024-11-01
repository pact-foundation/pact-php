<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\Message;

abstract class AbstractMessageBuilder implements BuilderInterface
{
    protected Message $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    /**
     * @param string                 $name      what is given to the request
     * @param array<string, mixed>  $params    for that request
     * @param bool                   $overwrite clear pass states completely and start this array
     */
    public function given(string $name, array $params = [], bool $overwrite = false): self
    {
        $this->message->setProviderState($name, $params, $overwrite);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     */
    public function expectsToReceive(string $description): self
    {
        $this->message->setDescription($description);

        return $this;
    }

    /**
     * @param array<string, string> $metadata what is the additional metadata of the message
     */
    public function withMetadata(array $metadata): self
    {
        $this->message->setMetadata($metadata);

        return $this;
    }

    /**
     * Make the http request to the Mock Service to register the message.  Content is required.
     *
     * @param mixed $contents required to be in the message
     */
    public function withContent(mixed $contents): self
    {
        $this->message->setContents($contents);

        return $this;
    }

    /**
     * Set key for message interaction. This feature only work with specification v4. It doesn't affect pact file with specification <= v3.
     */
    public function key(?string $key): self
    {
        $this->message->setKey($key);

        return $this;
    }

    /**
     * Mark the message interaction as pending. This feature only work with specification v4. It doesn't affect pact file with specification <= v3.
     */
    public function pending(?bool $pending): self
    {
        $this->message->setPending($pending);

        return $this;
    }

    /**
     * Set comments for the interaction.
     *
     * This is used by V4 interactions to set comments for the interaction. A
     * comment consists of a key-value pair, where the key is a string and the
     * value is anything that can be encoded as JSON.
     *
     * Args:
     *     key:
     *         Key for the comment.
     *
     *     value:
     *         Value for the comment. This must be encodable using
     *         `json_encode`, or an existing JSON string. The
     *         value of `null` will remove the comment with the given key.
     *
     *  # Warning
     *
     *  This function will overwrite any existing comment with the same key. In
     *  particular, the `text` key is used by {@see self::comment()}.
     *
     * @param array<string, mixed> $comments
     */
    public function comments(array $comments): self
    {
        $this->message->setComments($comments);

        return $this;
    }

    /**
     *  Add a text comment for the interaction.
     *
     *  This is used by V4 interactions to set arbitrary text comments for the
     *  interaction.
     *
     *  Args:
     *      comment:
     *          Text of the comment.
     *
     *  # Warning
     *
     *  Internally, the comments are appended to an array under the `text`
     *  comment key. Care should be taken to ensure that conflicts are not
     *  introduced by {@see self::comments()}.
     *
     * @param string $comment
     */
    public function comment(string $comment): self
    {
        $this->message->addTextComment($comment);

        return $this;
    }
}
