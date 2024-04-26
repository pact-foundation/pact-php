<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\DateTime;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testType(): void
    {
        $generator = new DateTime();
        $this->assertSame('DateTime', $generator->getType());
    }

    /**
     * @param array<string, string> $data
     */
    #[TestWith([null, null, []])]
    #[TestWith(["yyyy-MM-dd'T'HH:mm:ss", null, ['format' => "yyyy-MM-dd'T'HH:mm:ss"]])]
    #[TestWith([null, '+1 day', ['expression' => '+1 day']])]
    #[TestWith(["yyyy-MM-dd'T'HH:mm:ss", '+1 day', ['format' => "yyyy-MM-dd'T'HH:mm:ss", 'expression' => '+1 day']])]
    public function testAttributes(?string $format, ?string $expression, array $data): void
    {
        $generator = new DateTime($format, $expression);
        $attributes = $generator->getAttributes();
        $this->assertSame($generator, $attributes->getParent());
        $this->assertSame($data, $attributes->getData());
    }
}
