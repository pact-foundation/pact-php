<?php

namespace PhpPactTest\Helper\FFI;

use PhpPact\Consumer\Driver\Exception\InteractionCommentNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
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
}
