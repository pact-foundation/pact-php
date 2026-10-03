<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;

interface ParserInterface
{
    /**
     * @return array<string, array<int, string>>
     */
    public function parseHeaders(string $headers, bool $raw = false): array;

    public function parseBody(string $body, ?string $contentType = null): Text|Binary|null;

    /**
     * @return array<string, array<int, string>>
     */
    public function parseQueryString(string $query): array;

    /**
     * @param array<array-key, array<array-key, string>> $rows
     * @return array<string, string>
     */
    public function parseMetadataTable(array $rows): array;

    public function parseMetadataValue(string $value): string;

    /**
     * @return array<string, string>
     */
    public function parseMetadataMultiValues(string $values): array;
}
