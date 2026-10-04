<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

/**
 * A parsed Pact file. Mutations through Interaction/Message are
 * reflected when the Pact is serialized back with toJson()/toArray().
 */
final class Pact
{
    /**
     * @param array<array-key, mixed> $data
     */
    private function __construct(private array $data)
    {
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(is_array($data) ? $data : []);
    }

    public static function fromFile(string $path): self
    {
        return self::fromJson((string) file_get_contents($path));
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    public function toJson(): string
    {
        return (string) json_encode($this->data);
    }

    /**
     * @return list<Interaction>
     */
    public function getInteractions(): array
    {
        return array_values(array_map(
            fn (int $index): Interaction => new Interaction($this->data, $index),
            array_keys((array) ($this->data['interactions'] ?? []))
        ));
    }

    public function getInteraction(int $index = 0): Interaction
    {
        return new Interaction($this->data, $index);
    }

    /**
     * @return list<Message>
     */
    public function getMessages(): array
    {
        return array_values(array_map(
            fn (int $index): Message => new Message($this->data, $index),
            array_keys((array) ($this->data['messages'] ?? []))
        ));
    }

    public function getMessage(int $index = 0): Message
    {
        return new Message($this->data, $index);
    }
}
