<?php

namespace PhpPactTest\CompatibilitySuite\Service;

final class MatchingRulesStorage implements MatchingRulesStorageInterface
{
    /**
     * @var array<int, string>
     */
    private array $files = [];

    public function add(string $domain, int $id, string $file): void
    {
        $this->files[$domain][$id] = $file;
    }

    public function get(string $domain, int $id): ?string
    {
        return $this->files[$domain][$id] ?? null;
    }
}
