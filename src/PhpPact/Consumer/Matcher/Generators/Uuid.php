<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Exception\InvalidUuidFormatException;

/**
 * Generates a random UUID.
 * V4 supports specifying the format:
 * - simple (e.g 936DA01f9abd4d9d80c702af85c822a8)
 * - lower-case-hyphenated (e.g 936da01f-9abd-4d9d-80c7-02af85c822a8)
 * - upper-case-hyphenated (e.g 936DA01F-9ABD-4D9D-80C7-02AF85C822A8)
 * - URN (e.g. urn:uuid:936da01f-9abd-4d9d-80c7-02af85c822a8)
 */
class Uuid implements GeneratorInterface
{
    public const SIMPLE_FORMAT = 'simple';
    public const LOWER_CASE_HYPHENATED_FORMAT = 'lower-case-hyphenated';
    public const UPPER_CASE_HYPHENATED_FORMAT = 'upper-case-hyphenated';
    public const URN_FORMAT = 'URN';

    public const FORMATS = [
        self::SIMPLE_FORMAT,
        self::LOWER_CASE_HYPHENATED_FORMAT,
        self::UPPER_CASE_HYPHENATED_FORMAT,
        self::URN_FORMAT,
    ];

    public function __construct(private ?string $format = null)
    {
        if ($format && !in_array($format, self::FORMATS, true)) {
            throw new InvalidUuidFormatException(sprintf('Format %s is not supported. Supported formats are: %s', $format, implode(', ', self::FORMATS)));
        }
    }

    public function jsonSerialize(): object
    {
        $data = ['pact:generator:type' => 'Uuid'];

        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        return (object) $data;
    }
}
