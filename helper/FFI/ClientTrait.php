<?php

namespace PhpPactTest\Helper\FFI;

use PhpPact\Consumer\Driver\Exception\InteractionCommentNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionNotModifiedException;
use PhpPact\Consumer\Driver\Exception\InteractionPendingNotSetException;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\MockObject\MockObject;

trait ClientTrait
{
    protected ClientInterface&MockObject $client;

    /**
     * @param mixed[][] $calls
     */
    protected function assertClientCalls(array $calls): void
    {
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('call')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }

                return $return;
            });
    }

    protected function expectsSetInteractionKey(int $interaction, string $description, ?string $key, bool $result): void
    {
        $this->client
            ->expects($this->exactly($key === null ? 0 : 1))
            ->method('setKey')
            ->with($interaction, $key)
            ->willReturn($result);
        if (!$result) {
            $this->expectException(InteractionKeyNotSetException::class);
            $this->expectExceptionMessage("Can not set the key '$key' for the interaction '{$description}'");
        }
    }

    protected function expectsSetInteractionPending(int $interaction, string $description, ?bool $pending, bool $result): void
    {
        $this->client
            ->expects($this->exactly($pending === null ? 0 : 1))
            ->method('setPending')
            ->with($interaction, $pending)
            ->willReturn($result);
        if (!$result) {
            $this->expectException(InteractionPendingNotSetException::class);
            $this->expectExceptionMessage("Can not mark interaction '{$description}' as pending");
        }
    }

    /**
     * @param array<string, mixed> $comments
     */
    protected function expectsSetComments(int $interaction, string $description, array $comments, bool $result): void
    {
        $calls = [];
        $lastKey = array_key_last($comments);
        foreach ($comments as $key => $value) {
            $calls[] = [$interaction, $key, (is_string($value) || is_null($value)) ? $value : json_encode($value), $key === $lastKey ? $result : true];
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('setComment')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }

                return $return;
            });
        if (!$result) {
            $this->expectException(InteractionCommentNotSetException::class);
            $this->expectExceptionMessage("Can not add comment '$key' to the interaction '{$description}'");
        }
    }

    /**
     * @param string[] $comments
     */
    protected function expectsAddTextComments(int $interaction, string $description, array $comments, bool $result): void
    {
        $calls = [];
        $lastKey = array_key_last($comments);
        foreach ($comments as $key => $comment) {
            $calls[] = [$interaction, $comment, $key === $lastKey ? $result : true];
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('addTextComment')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }

                return $return;
            });
        if (!$result) {
            $this->expectException(InteractionCommentNotSetException::class);
            $this->expectExceptionMessage("Can not add text comment '$comment' to the interaction '{$description}'");
        }
    }

    protected function expectsNewInteraction(int $pact, string $description, int $interaction): void
    {
        $this->client
            ->expects($this->once())
            ->method('newInteraction')
            ->with($pact, $description)
            ->willReturn($interaction);
    }

    protected function expectsNewMessageInteraction(int $pact, string $description, int $interaction): void
    {
        $this->client
            ->expects($this->once())
            ->method('newMessageInteraction')
            ->with($pact, $description)
            ->willReturn($interaction);
    }

    protected function expectsNewSyncMessageInteraction(int $pact, string $description, int $interaction): void
    {
        $this->client
            ->expects($this->once())
            ->method('newSyncMessageInteraction')
            ->with($pact, $description)
            ->willReturn($interaction);
    }

    protected function expectsGiven(int $interaction, string $name, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('given')
            ->with($interaction, $name)
            ->willReturn($result);
        if (!$result) {
            $this->expectException(InteractionNotModifiedException::class);
            $this->expectExceptionMessage("The interaction or Pact can't be modified (i.e. the mock server for it has already started)");
        }
    }

    /**
     * @param array<string, string> $params
     */
    protected function expectsGivenWithParam(int $interaction, string $name, array $params, bool $result): void
    {
        $calls = [];
        $lastKey = array_key_last($params);
        foreach ($params as $key => $value) {
            $calls[] = [$interaction, $name, $key, $value, $key === $lastKey ? $result : true];
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('givenWithParam')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }

                return $return;
            });
        if (!$result) {
            $this->expectException(InteractionNotModifiedException::class);
            $this->expectExceptionMessage("The interaction or Pact can't be modified (i.e. the mock server for it has already started)");
        }
    }

    protected function expectsUponReceiving(int $interaction, string $description, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('uponReceiving')
            ->with($interaction, $description)
            ->willReturn($result);
        if (!$result) {
            $this->expectException(InteractionNotModifiedException::class);
            $this->expectExceptionMessage("The interaction or Pact can't be modified (i.e. the mock server for it has already started)");
        }
    }

    protected function expectsMessageExpectsToReceive(int $message, string $description): void
    {
        $this->client
            ->expects($this->once())
            ->method('messageExpectsToReceive')
            ->with($message, $description);
    }

    /**
     * @param array<string, string> $metadata
     */
    protected function expectsMessageWithMetadataV2(int $message, array $metadata): void
    {
        $calls = [];
        foreach ($metadata as $key => $value) {
            $calls[] = [$message, $key, $value];
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('messageWithMetadataV2')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }
            });
    }

    protected function expectsMessageGiven(int $message, string $name): void
    {
        $this->client
            ->expects($this->once())
            ->method('messageGiven')
            ->with($message, $name);
    }

    /**
     * @param array<string, string> $params
     */
    protected function expectsMessageGivenWithParam(int $message, string $name, array $params): void
    {
        $calls = [];
        foreach ($params as $key => $value) {
            $calls[] = [$message, $name, $key, $value];
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('messageGivenWithParam')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }
            });
    }
}
