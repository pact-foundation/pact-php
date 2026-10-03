<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPactTest\CompatibilitySuite\Exception\InvalidXmlFixtureException;
use PhpPactTest\CompatibilitySuite\Model\Xml;

final class Parser implements ParserInterface
{
    public const SINGLE_VALUE_HEADERS = [
        "date",
        "accept-datetime",
        "if-modified-since",
        "if-unmodified-since",
        "expires",
        "retry-after",
        "last-modified"
    ];

    public function __construct(
        private FixtureLoaderInterface $fixtureLoader,
        private string $specificationVersion
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function parseHeaders(string $headers, bool $raw = false): array
    {
        if (empty($headers)) {
            return [];
        }
        $values = [];
        foreach (explode(',', $headers) as $headerLine) {
            [$header, $value] = explode(':', rtrim(ltrim(trim($headerLine), "'"), "'"), 2);

            $header = trim($header);
            $value = trim($value);

            if ($raw || in_array(strtolower($header), self::SINGLE_VALUE_HEADERS, true)) {
                $values[$header][] = $value;
            } else {
                $values[$header] = array_merge($values[$header] ?? [], array_map(fn (string $value) => trim($value), explode(',', $value)));
            }
        }

        return $values;
    }

    public function parseBody(string $body, ?string $contentType = null): Text|Binary|null
    {
        if (empty($body)) {
            return null;
        }
        if (str_starts_with($body, 'JSON:')) {
            return new Text(trim(substr($body, 5)), 'application/json');
        }
        if (str_starts_with($body, 'XML:')) {
            return new Text(trim(substr($body, 4)), 'application/xml');
        }
        if (str_starts_with($body, 'file:')) {
            $fileName = trim(substr($body, 5));
            $contents = $this->fixtureLoader->load($fileName);
            if (str_ends_with($fileName, '-body.xml')) {
                $body = simplexml_load_string($contents);
                if (!$body) {
                    throw new InvalidXmlFixtureException(sprintf("could not read fixture '%s'", $fileName));
                }
                $contentType = isset($body->contentType) ? (string) $body->contentType : 'text/plain';
                $lineEndings = (string) (iterator_to_array($body->contents->attributes())['eol'] ?? '');
                $contents = (string) $body->contents;

                if ($lineEndings === 'CRLF' && PHP_OS_FAMILY !== 'Windows') {
                    $contents = str_replace("\n", "\r\n", $contents);
                }

                if ($this->specificationVersion !== '1.0.0' && $contentType === 'application/xml') {
                    return new Xml($contents, $contentType);
                } else {
                    return new Text($contents, $contentType);
                }
            } else {
                $contentType ??= $this->fixtureLoader->determineContentType($fileName);
                $isBinary = $this->fixtureLoader->isBinary($fileName);
                $filePath = $this->fixtureLoader->getFilePath($fileName);

                return $isBinary ? new Binary($filePath, $contentType) : new Text($contents, $contentType);
            }
        }
        if ($body === 'EMPTY') {
            $body = '';
        }

        return new Text($body, $contentType ?? 'text/plain');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function parseQueryString(string $query): array
    {
        if (empty($query)) {
            return [];
        }

        $values = [];
        foreach (explode('&', $query) as $kv) {
            if (str_contains($kv, '=')) {
                [$key, $value] = explode('=', $kv, 2);
                $values[$key][] = $value;
            } else {
                $values[$kv][] = '';
            }
        }

        return $values;
    }

    /**
     * @param array<array-key, array<array-key, string>> $rows
     * @return array<string, string>
     */
    public function parseMetadataTable(array $rows): array
    {
        $metadata = [];
        foreach ($rows as $row) {
            $metadata[$row['key']] = str_starts_with($row['value'], 'JSON: ') ? substr($row['value'], 6) : $row['value'];
        }

        return $metadata;
    }

    public function parseMetadataValue(string $value): string
    {
        $value = str_starts_with($value, 'JSON: ') ? substr($value, 6) : $value;
        $value = str_replace('\"', '"', $value);

        return $value;
    }

    /**
     * @return array<string, string>
     */
    public function parseMetadataMultiValues(string $items): array
    {
        $metadata = [];
        foreach (explode(';', $items) as $item) {
            [$key, $value] = explode('=', trim($item));
            $metadata[$key] = str_starts_with($value, 'JSON: ') ? substr($value, 6) : $value;
        }

        return $metadata;
    }
}
