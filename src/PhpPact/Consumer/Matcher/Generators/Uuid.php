<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Enum\UuidFormat;
use PhpPact\Consumer\Matcher\Exception\InvalidUuidFormatException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random UUID.
 * V4 supports specifying the format:
 * - simple (e.g 936DA01f9abd4d9d80c702af85c822a8)
 * - lower-case-hyphenated (e.g 936da01f-9abd-4d9d-80c7-02af85c822a8)
 * - upper-case-hyphenated (e.g 936DA01F-9ABD-4D9D-80C7-02AF85C822A8)
 * - URN (e.g. urn:uuid:936da01f-9abd-4d9d-80c7-02af85c822a8)
 */
class Uuid implements GeneratorInterface, JsonFormattableInterface
{
    /**
     * @deprecated Use UuidFormat::SIMPLE instead
     */
    public const SIMPLE_FORMAT = 'simple';
    /**
     * @deprecated Use UuidFormat::LOWER_CASE_HYPHENATED instead
     */
    public const LOWER_CASE_HYPHENATED_FORMAT = 'lower-case-hyphenated';
    /**
     * @deprecated Use UuidFormat::UPPER_CASE_HYPHENATED instead
     */
    public const UPPER_CASE_HYPHENATED_FORMAT = 'upper-case-hyphenated';
    /**
     * @deprecated Use UuidFormat::URN instead
     */
    public const URN_FORMAT = 'URN';

    private null|UuidFormat $format;

    /**
     * @param null|string|UuidFormat $format Default to lower-case-hyphenated if null
     */
    public function __construct(null|string|UuidFormat $format = null)
    {
        if (is_string($format)) {
            try {
                $format = UuidFormat::from($format);
            } catch (\Throwable $th) {
                $all = implode(', ', array_map(
                    fn (UuidFormat $status) => $status->value,
                    UuidFormat::cases()
                ));
                throw new InvalidUuidFormatException(sprintf('Format %s is not supported. Supported formats are: %s', $format, $all));
            }
        }
        $this->format = $format;
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'Uuid',
            ...(is_null($this->format) ? [] : ['format' => $this->format->value]),
        ]);
    }
}
