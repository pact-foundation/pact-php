<?php

namespace PhpPactTest\Helper\FFI;

use FFI\CData;
use PhpPact\Consumer\Driver\Exception\InteractionCommentNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionNotModifiedException;
use PhpPact\Consumer\Driver\Exception\InteractionPendingNotSetException;
use PhpPact\Consumer\Driver\Exception\PactFileNotWrittenException;
use PhpPact\Consumer\Driver\Exception\PactNotModifiedException;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Exception\MockServerPactFileNotWrittenException;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Model\ArrayData;
use PhpPact\Plugin\Exception\PluginBodyNotAddedException;
use PhpPact\Plugin\Exception\PluginNotLoadedException;
use PhpPact\Standalone\ProviderVerifier\Exception\VerifierNotCreatedException;
use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\MockObject\MockObject;

trait ClientTrait
{
    protected ClientInterface&MockObject $client;

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
            $this->expectExceptionMessage("The interaction can't be modified (i.e. the mock server for it has already started)");
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
            $this->expectExceptionMessage("The interaction can't be modified (i.e. the mock server for it has already started)");
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
            $this->expectExceptionMessage("The interaction can't be modified (i.e. the mock server for it has already started)");
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

    protected function expectsFreePactHandle(int $pact, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('freePactHandle')
            ->with($pact)
            ->willReturn($result);
    }

    protected function expectsNewPact(string $consumer, string $provider, int $pact): void
    {
        $this->client
            ->expects($this->once())
            ->method('newPact')
            ->with($consumer, $provider)
            ->willReturn($pact);
    }

    protected function expectsWithSpecification(int $pact, int $specification, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('withSpecification')
            ->with($pact, $specification)
            ->willReturn($result);
        if (!$result) {
            $this->expectException(PactNotModifiedException::class);
            $this->expectExceptionMessage("The pact can't be modified (i.e. the mock server for it has already started, or the version is invalid)");
        }
    }

    protected function expectsInitWithLogLevel(?string $logLevel): void
    {
        if ($logLevel) {
            $this->client
                ->expects($this->once())
                ->method('initWithLogLevel')
                ->with($logLevel);
        } else {
            $this->client
                ->expects($this->never())
                ->method('initWithLogLevel');
        }
    }

    protected function expectsPactHandleWriteFile(int $pact, string $directory, bool $overwrite, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('pactHandleWriteFile')
            ->with($pact, $directory, $overwrite)
            ->willReturn($result);
        if ($result) {
            $this->expectException(PactFileNotWrittenException::class);
            $this->expectExceptionMessage(match ($result) {
                1 => 'The function panicked.',
                2 => 'The pact file was not able to be written.',
                3 => 'The pact for the given handle was not found.',
                default => 'Unknown error',
            });
        }
    }

    protected function expectsCleanupPlugins(int $pact): void
    {
        $this->client
            ->expects($this->once())
            ->method('cleanupPlugins')
            ->with($pact);
    }

    protected function expectsUsingPlugin(int $pact, string $name, ?string $version, int $result, bool $supported): void
    {
        if ($supported) {
            $this->client
                ->expects($this->once())
                ->method('usingPlugin')
                ->with($pact, $name, $version)
                ->willReturn($result);
        } else {
            $this->client
                ->expects($this->never())
                ->method('usingPlugin');
        }
        if ($supported && $result) {
            $this->expectException(PluginNotLoadedException::class);
            $this->expectExceptionMessage(match ($result) {
                1 => 'A general panic was caught.',
                2 => 'Failed to load the plugin.',
                3 => 'Pact Handle is not valid.',
                default => 'Unknown error',
            });
        }
    }

    protected function expectsCleanupMockServer(int $port, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('cleanupMockServer')
            ->with($port)
            ->willReturn($result);
    }

    protected function expectsMockServerMatched(int $port, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('mockServerMatched')
            ->with($port)
            ->willReturn($result);
    }

    protected function expectsMockServerMismatches(int $port, string $result, bool $matched): void
    {
        if (!$matched) {
            $this->client
                ->expects($this->once())
                ->method('mockServerMismatches')
                ->with($port)
                ->willReturn($result);
        } else {
            $this->client
                ->expects($this->never())
                ->method('mockServerMismatches');
        }
    }

    protected function expectsWritePactFile(int $port, string $directory, bool $overwrite, int $result, bool $matched): void
    {
        if ($matched) {
            $this->client
                ->expects($this->once())
                ->method('writePactFile')
                ->with($port, $directory, $overwrite)
                ->willReturn($result);
            if ($result) {
                $this->expectException(MockServerPactFileNotWrittenException::class);
                $this->expectExceptionMessage(match ($result) {
                    1 => 'A general panic was caught',
                    2 => 'The pact file was not able to be written',
                    3 => 'A mock server with the provided port was not found',
                    default => 'Unknown error',
                });
            }
        } else {
            $this->client
                ->expects($this->never())
                ->method('writePactFile');
        }
    }

    protected function expectsCreateMockServerForTransport(int $pact, string $host, int $port, string $transport, ?string $transportConfig, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('createMockServerForTransport')
            ->with($pact, $host, $port, $transport, $transportConfig)
            ->willReturn($result);
        if ($result < 0) {
            $this->expectException(MockServerNotStartedException::class);
            $this->expectExceptionMessage(match ($result) {
                -1 => 'An invalid handle was received. Handles should be created with `pactffi_new_pact`',
                -2 => 'Transport_config is not valid JSON',
                -3 => 'The mock server could not be started',
                -4 => 'The method panicked',
                -5 => 'The address is not valid',
                default => 'Unknown error',
            });
        }
    }

    protected function expectsVerifierNewForApplication(string $name, string $version, ?CData $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierNewForApplication')
            ->with($name, $version)
            ->willReturn($result);
        if (!$result) {
            $this->expectException(VerifierNotCreatedException::class);
        }
    }

    protected function expectsVerifierSetProviderInfo(CData $handle, ?string $name, ?string $scheme, ?string $host, ?int $port, ?string $path): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierSetProviderInfo')
            ->with($handle, $name, $scheme, $host, $port, $path);
    }

    protected function expectsVerifierAddProviderTransport(CData $handle, ?string $protocol, ?int $port, ?string $path, ?string $scheme): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierAddProviderTransport')
            ->with($handle, $protocol, $port, $path, $scheme);
    }

    protected function expectsVerifierSetFilterInfo(CData $handle, ?string $filterDescription, ?string $filterState, bool $filterNoState): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierSetFilterInfo')
            ->with($handle, $filterDescription, $filterState, $filterNoState);
    }

    protected function expectsVerifierSetProviderState(CData $handle, ?string $url, bool $teardown, bool $body): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierSetProviderState')
            ->with($handle, $url, $teardown, $body);
    }

    protected function expectsVerifierSetVerificationOptions(CData $handle, bool $disableSslVerification, int $requestTimeout, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierSetVerificationOptions')
            ->with($handle, $disableSslVerification, $requestTimeout)
            ->willReturn($result);
    }

    /**
     * @param string[] $providerTags
     */
    protected function expectsVerifierSetPublishOptions(CData $handle, string $providerVersion, ?string $buildUrl, array $providerTags, ?string $providerBranch, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierSetPublishOptions')
            ->with(
                $handle,
                $providerVersion,
                $buildUrl,
                count($providerTags) > 0 ? $this->isInstanceOf(ArrayData::class) : null,
                $providerBranch
            )
            ->willReturn($result);
    }

    /**
     * @param string[] $consumerFilters
     */
    protected function expectsVerifierSetConsumerFilters(CData $handle, array $consumerFilters): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierSetConsumerFilters')
            ->with(
                $handle,
                count($consumerFilters) > 0 ? $this->isInstanceOf(ArrayData::class) : null
            );
    }

    /**
     * @param array<string, string> $customHeaders
     */
    protected function expectsVerifierAddCustomHeader(CData $handle, array $customHeaders): void
    {
        $calls = [];
        foreach ($customHeaders as $name => $value) {
            $calls[] = [$handle, $name, $value];
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('verifierAddCustomHeader')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }
            });
    }

    protected function expectsVerifierAddFileSource(CData $handle, string $file): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierAddFileSource')
            ->with($handle, $file);
    }

    protected function expectsVerifierAddDirectorySource(CData $handle, string $directory): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierAddDirectorySource')
            ->with($handle, $directory);
    }

    protected function expectsVerifierAddUrlSource(CData $handle, string $url, ?string $username, ?string $password, ?string $token): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierAddUrlSource')
            ->with($handle, $url, $username, $password, $token);
    }

    protected function expectsVerifierBrokerSourceWithSelectors(
        CData $handle,
        string $url,
        ?string $username,
        ?string $password,
        ?string $token,
        bool $enablePending,
        ?string $includeWipPactsSince,
        array $providerTags,
        ?string $providerBranch,
        ConsumerVersionSelectors $consumerVersionSelectors,
        array $consumerVersionTags
    ): void {
        $this->client
            ->expects($this->once())
            ->method('verifierBrokerSourceWithSelectors')
            ->with(
                $handle,
                $url,
                $username,
                $password,
                $token,
                $enablePending,
                $includeWipPactsSince,
                count($providerTags) > 0 ? $this->isInstanceOf(ArrayData::class) : null,
                $providerBranch,
                count($consumerVersionSelectors) > 0 ? $this->isInstanceOf(ArrayData::class) : null,
                count($consumerVersionTags) > 0 ? $this->isInstanceOf(ArrayData::class) : null
            );
    }

    protected function expectsVerifierExecute(CData $handle, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierExecute')
            ->with($handle)
            ->willReturn($result);
    }

    protected function expectsVerifierJson(CData $handle, bool $hasLogger, ?string $result): void
    {
        if ($hasLogger) {
            $this->client
                ->expects($this->once())
                ->method('verifierJson')
                ->with($handle)
                ->willReturn($result);
        } else {
            $this->client
                ->expects($this->never())
                ->method('verifierJson');
        }
    }

    protected function expectsVerifierShutdown(CData $handle): void
    {
        $this->client
            ->expects($this->once())
            ->method('verifierShutdown')
            ->with($handle);
    }

    protected function expectsMessageReify(int $message, string $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('messageReify')
            ->with($message)
            ->willReturn($result);
    }

    /**
     * @param array<string, string[]> $headers
     */
    protected function expectsWithHeaderV2(int $interaction, int $part, array $headers): void
    {
        $calls = [];
        foreach ($headers as $name => $values) {
            foreach ($values as $index => $value) {
                $calls[] = [$interaction, $part, $name, $index, $value, true];
            }
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('withHeaderV2')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }

                return $return;
            });
    }

    /**
     * @param array<string, string[]> $query
     */
    protected function expectsWithQueryParameterV2(int $interaction, array $query): void
    {
        $calls = [];
        foreach ($query as $name => $values) {
            foreach ($values as $index => $value) {
                $calls[] = [$interaction, $name, $index, $value, true];
            }
        }
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('withQueryParameterV2')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                foreach ($args as $key => $arg) {
                    $this->assertThat($arg, $call[$key] instanceof Constraint ? $call[$key] : new IsIdentical($call[$key]));
                }

                return $return;
            });
    }

    protected function expectsWithRequest(int $interaction, ?string $method, ?string $path, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('withRequest')
            ->with($interaction, $method, $path)
            ->willReturn($result);
    }

    protected function expectsResponseStatusV2(int $interaction, ?string $status, bool $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('responseStatusV2')
            ->with($interaction, $status)
            ->willReturn($result);
    }

    protected function expectsInteractionContents(int $interaction, int $part, string $contentType, string $contents, int $result): void
    {
        $this->client
            ->expects($this->once())
            ->method('interactionContents')
            ->with($interaction, $part, $contentType, $contents)
            ->willReturn($result);
        if ($result > 0) {
            $this->expectException(PluginBodyNotAddedException::class);
            $this->expectExceptionMessage(match ($result) {
                1 => 'A general panic was caught.',
                2 => 'The mock server has already been started.',
                3 => 'The interaction handle is invalid.',
                4 => 'The content type is not valid.',
                5 => 'The contents JSON is not valid JSON.',
                6 => 'The plugin returned an error.',
                default => 'Unknown error',
            });
        }
    }
}
