<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\BooleanFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Match if the value is a boolean value (booleans and the string values `true` and `false`)
 */
class Boolean extends GeneratorAwareMatcher
{
    public function __construct(private ?bool $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomBoolean());
        }
        parent::__construct();
    }

    public function getType(): string
    {
        return 'boolean';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): ?bool
    {
        return $this->value;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new HasGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new BooleanFormatter();
    }
}
