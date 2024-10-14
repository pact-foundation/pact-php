<?php

namespace PhpPact\Consumer\Matcher\Model;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use PhpPact\Consumer\Matcher\Exception\AttributeConflictException;
use Traversable;

/**
 * @implements IteratorAggregate<string, mixed>
 */
class Attributes implements IteratorAggregate, JsonSerializable
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data = [])
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function merge(self $attributes): self
    {
        $return = new self($this->data);
        foreach ($attributes as $key => $value) {
            if (!$return->has($key)) {
                $return->set($key, $value);
            } elseif ($return->has($key) && $value !== $return->get($key)) {
                throw new AttributeConflictException(sprintf("Can not merge attributes: Values of attribute '%s' are conflict", $key));
            }
        }

        return $return;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
