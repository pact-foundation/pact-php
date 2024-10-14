<?php

namespace PhpPactTest\Consumer\Matcher\Model;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Model\Expression;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    public function testFormat(): void
    {
        $format = 'values(%quote%, %noQuote%, %true%, %false%, %integer%, %double%, %empty%)';
        $values = [
            'quote' => "it's fine",
            'noQuote' => 'right',
            'true' => true,
            'false' => false,
            'integer' => -123,
            'double' => -12.3,
            'empty' => null,
        ];
        $expression = new Expression($format, $values);
        $this->assertSame($format, $expression->getFormat());
        $this->assertSame($values, $expression->getValues());
        $this->assertSame("\"values('it\\\'s fine', 'right', true, false, -123, -12.3, null)\"", json_encode($expression));
        $this->assertSame("values('it\'s fine', 'right', true, false, -123, -12.3, null)", (string)$expression);
    }

    #[TestWith([new \stdClass(), 'object'])]
    #[TestWith([['key' => 'value'], 'array'])]
    public function testInvalidValue(mixed $value, string $type): void
    {
        $expression = new Expression('%key%', ['key' => $value]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        json_encode($expression);
    }
}
