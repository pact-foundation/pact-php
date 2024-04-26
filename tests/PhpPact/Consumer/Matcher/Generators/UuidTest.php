<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Exception\InvalidUuidFormatException;
use PhpPact\Consumer\Matcher\Generators\Uuid;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testType(): void
    {
        $generator = new Uuid();
        $this->assertSame('Uuid', $generator->getType());
    }

    /**
     * @param null|array<string, string> $data
     */
    #[TestWith([null, []])]
    #[TestWith(['simple', ['format' => 'simple']])]
    #[TestWith(['lower-case-hyphenated', ['format' => 'lower-case-hyphenated']])]
    #[TestWith(['upper-case-hyphenated', ['format' => 'upper-case-hyphenated']])]
    #[TestWith(['URN', ['format' => 'URN']])]
    #[TestWith(['invalid', null])]
    public function testAttributes(?string $format, ?array $data): void
    {
        if (null === $data) {
            $this->expectException(InvalidUuidFormatException::class);
            $this->expectExceptionMessage('Format invalid is not supported. Supported formats are: simple, lower-case-hyphenated, upper-case-hyphenated, URN');
        }
        $generator = new Uuid($format);
        $attributes = $generator->getAttributes();
        $this->assertSame($generator, $attributes->getParent());
        $this->assertSame($data, $attributes->getData());
    }
}
