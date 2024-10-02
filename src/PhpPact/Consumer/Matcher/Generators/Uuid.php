<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Enum\UuidFormat;
use PhpPact\Consumer\Matcher\Exception\InvalidUuidFormatException;

/**
 * Generates a random UUID.
 * V4 supports specifying the format:
 * - simple (e.g 936DA01f9abd4d9d80c702af85c822a8)
 * - lower-case-hyphenated (e.g 936da01f-9abd-4d9d-80c7-02af85c822a8)
 * - upper-case-hyphenated (e.g 936DA01F-9ABD-4D9D-80C7-02AF85C822A8)
 * - URN (e.g. urn:uuid:936da01f-9abd-4d9d-80c7-02af85c822a8)
 */
class Uuid extends AbstractGenerator
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

    public function getType(): string
    {
        return 'Uuid';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return $this->format !== null ? [
            'format' => $this->format->value,
        ] : [];
    }
}
