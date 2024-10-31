<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactory;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Build an interaction and send it to the Ruby Standalone Mock Service
 */
class InteractionBuilder implements BuilderInterface
{
    private InteractionDriverInterface $driver;
    private Interaction $interaction;

    public function __construct(MockServerConfigInterface $config, ?InteractionDriverFactoryInterface $driverFactory = null)
    {
        $this->driver      = ($driverFactory ?? new InteractionDriverFactory())->create($config);
        $this->newInteraction();
    }

    public function newInteraction(): void
    {
        $this->interaction = new Interaction();
    }

    /**
     * @param string $providerState what is given to the request
     * @param array<string, mixed>  $params    for that request
     * @param bool   $overwrite clear pass states completely and start this array
     */
    public function given(string $providerState, array $params = [], bool $overwrite = false): self
    {
        $this->interaction->setProviderState($providerState, $params, $overwrite);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     */
    public function uponReceiving(string $description): self
    {
        $this->interaction->setDescription($description);

        return $this;
    }

    /**
     * @param ConsumerRequest $request mock of request sent
     */
    public function with(ConsumerRequest $request): self
    {
        $this->interaction->setRequest($request);

        return $this;
    }

    /**
     * @param ProviderResponse $response mock of response received
     * @param bool             $startMockServer start mock server. Can't register more interaction if mock server is started
     *
     * @return bool returns true on success
     */
    public function willRespondWith(ProviderResponse $response, bool $startMockServer = true): bool
    {
        $this->interaction->setResponse($response);

        return $this->driver->registerInteraction($this->interaction, $startMockServer);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(): bool
    {
        return $this->driver->verifyInteractions()->matched;
    }

    /**
     * Set key for interaction. This feature only work with specification v4. It doesn't affect pact file with specification <= v3.
     */
    public function key(?string $key): self
    {
        $this->interaction->setKey($key);

        return $this;
    }

    /**
     * Mark the interaction as pending. This feature only work with specification v4. It doesn't affect pact file with specification <= v3.
     */
    public function pending(?bool $pending): self
    {
        $this->interaction->setPending($pending);

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
        $this->interaction->setComments($comments);

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
        $this->interaction->addTextComment($comment);

        return $this;
    }
}
