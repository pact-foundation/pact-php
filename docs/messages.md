# Message support

The goal is not to test the transmission of an object over a bus but instead vet the contents of the message.
While examples included focus on a Rabbit MQ, the exact message queue is irrelevant. Initial comparisons require a certain
object type to be created by the Publisher/Producer and the Consumer of the message.  This includes a metadata set where you
can store the key, queue, exchange, etc that the Publisher and Consumer agree on.  The content format needs to be JSON.

To take advantage of the existing pact-verification tools, the provider side of the equation stands up an http proxy to callback
to processing class.   Aside from changing default ports, this should be transparent to the users of the libary.

Both the provider and consumer side make heavy use of lambda functions.

## Consumer Side Message Processing

The examples provided are pretty basic.   See [example](../example/message/consumer/tests/ExampleMessageConsumerTest.php).
1. Create the content and metadata (array)
1. Annotate the MessageBuilder appropriate content and states
    1. Given = Provider State
    1. expectsToReceive = Description
1. Set the callback you want to run when a message is provided
    1. The callback must accept a JSON string as a parameter
1. Run Verify.  If nothing blows up, #winning.

```php
$builder    = new MessageBuilder(self::$config);

$contents       = new \stdClass();
$contents->song = 'And the wind whispers Mary';

$metadata = ['queue'=>'And the clowns have all gone to bed', 'routing_key'=>'And the clowns have all gone to bed'];

$builder
    ->given('You can hear happiness staggering on down the street')
    ->expectsToReceive('footprints dressed in red')
    ->withMetadata($metadata)
    ->withContent($contents);

// established mechanism to this via callbacks
$consumerMessage = new ExampleMessageConsumer();
$callback        = [$consumerMessage, 'ProcessSong'];
$builder->setCallback($callback);

$verifyResult = $builder->verify();

$this->assertTrue($verifyResult);
```

## Provider Side Message Validation

Handle these requests on your provider:

1. POST /pact-change-state
   1. Set up your database to meet the expectations of the request
   2. Reset the database to its original state.
2. POST /pact-messages
   1. Return message's content in body
   2. Return message's metadata in header `PACT-MESSAGE-METADATA`

[Click here](../example/message/provider/public/index.php) to see the full sample file.
