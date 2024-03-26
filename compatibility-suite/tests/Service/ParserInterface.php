<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;

interface ParserInterface
{
    public function parseHeaders(string $headers, bool $raw = false): array;

    public function parseBody(string $body, ?string $contentType = null): Text|Binary|Multipart|null;

    public function parseQueryString(string $query): array;

    public function parseMetadataTable(array $rows): array;

    public function parseMetadataValue(string $value): string;

    public function parseMetadataMultiValues(string $values): array;
}
