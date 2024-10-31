<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Exception\BodyNotSupportedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\ProviderState;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    private Message $message;

    public function setUp(): void
    {
        $this->message = new Message();
    }

    public function testSetters(): void
    {
        $handle              = 123;
        $description         = 'a message';
        $providerStateName   = 'a provider state';
        $providerStateParams = ['foo' => 'bar'];
        $metadata            = ['queue' => 'foo', 'routing_key' => 'bar'];
        $contents            = 'test';

        $subject = $this->message
            ->setHandle($handle)
            ->setDescription($description)
            ->addProviderState($providerStateName, $providerStateParams)
            ->setMetadata($metadata)
            ->setContents($contents);

        static::assertSame($handle, $subject->getHandle());
        static::assertSame($description, $subject->getDescription());
        $providerStates = $subject->getProviderStates();
        static::assertCount(1, $providerStates);
        static::assertContainsOnlyInstancesOf(ProviderState::class, $providerStates);
        static::assertEquals($providerStateName, $providerStates[0]->getName());
        static::assertEquals($providerStateParams, $providerStates[0]->getParams());
        static::assertSame($metadata, $subject->getMetadata());

        $messageContents = $subject->getContents();
        $this->assertInstanceOf(Text::class, $messageContents);
        $this->assertEquals($contents, $messageContents->getContents());
        $this->assertEquals('text/plain', $messageContents->getContentType());
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testSetProviderState(bool $overwrite): void
    {
        $this->message->setProviderState('provider state 1', ['key 1' => 'value 1'], true);
        $providerStates = $this->message->setProviderState('provider state 2', ['key 2' => 'value 2'], $overwrite);
        if ($overwrite) {
            $this->assertCount(1, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        } else {
            $this->assertCount(2, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 1', $providerState->getName());
            $this->assertSame(['key 1' => 'value 1'], $providerState->getParams());
            $providerState = end($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        }
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testAddProviderState(bool $overwrite): void
    {
        $this->assertSame($this->message, $this->message->addProviderState('provider state 1', ['key 1' => 'value 1'], true));
        $this->assertSame($this->message, $this->message->addProviderState('provider state 2', ['key 2' => 'value 2'], $overwrite));
        $providerStates = $this->message->getProviderStates();
        if ($overwrite) {
            $this->assertCount(1, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        } else {
            $this->assertCount(2, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 1', $providerState->getName());
            $this->assertSame(['key 1' => 'value 1'], $providerState->getParams());
            $providerState = end($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        }
    }

    #[TestWith([null])]
    #[TestWith([new Text('column1,column2,column3', 'text/csv')])]
    #[TestWith([new Binary('/path/to/image.png', 'image/png')])]
    public function testContents(mixed $contents): void
    {
        $this->assertSame($this->message, $this->message->setContents($contents));
        $this->assertSame($contents, $this->message->getContents());
    }

    public function testTextContents(): void
    {
        $text = 'example text';
        $this->assertSame($this->message, $this->message->setContents($text));
        $contents = $this->message->getContents();
        $this->assertInstanceOf(Text::class, $contents);
        $this->assertSame($text, $contents->getContents());
        $this->assertSame('text/plain', $contents->getContentType());
    }

    public function testJsonContents(): void
    {
        $array = ['key' => 'value'];
        $this->assertSame($this->message, $this->message->setContents($array));
        $contents = $this->message->getContents();
        $this->assertInstanceOf(Text::class, $contents);
        $this->assertSame('{"key":"value"}', $contents->getContents());
        $this->assertSame('application/json', $contents->getContentType());
    }

    public function testMultipartContents(): void
    {
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Message does not support multipart');
        $multipart = new Multipart([], 'abc123');
        $this->message->setContents($multipart);
    }

    public function testAddProviderStateWithParamMixedValue(): void
    {
        $this->message->addProviderState('provider state 1', ['string value' => 'test']);
        $this->message->addProviderState('provider state 2', ['number value' => 123]);
        $this->message->addProviderState('provider state 3', ['array value' => ['value 1', 'value 2', 'value 3']]);
        $this->message->addProviderState('provider state 4', ['object value' => (object) ['key 1' => 'value 1', 'key 2' => 'value 2']]);
        $providerStates = $this->message->getProviderStates();
        $this->assertCount(4, $providerStates);
        $providerState = reset($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 1', $providerState->getName());
        $this->assertSame(['string value' => 'test'], $providerState->getParams());
        $providerState = next($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 2', $providerState->getName());
        $this->assertSame(['number value' => '123'], $providerState->getParams());
        $providerState = next($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 3', $providerState->getName());
        $this->assertSame(['array value' => '["value 1","value 2","value 3"]'], $providerState->getParams());
        $providerState = next($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 4', $providerState->getName());
        $this->assertSame(['object value' => '{"key 1":"value 1","key 2":"value 2"}'], $providerState->getParams());
    }
}
