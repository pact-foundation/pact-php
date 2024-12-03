<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\ExpressionFormattableTrait;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

abstract class AbstractDateTime extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;
    use ExpressionFormattableTrait;

    public function __construct(protected string $format, private string $value = '')
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => $this->getType(),
            'format' => $this->format,
            'value' => $this->value,
        ]));
    }

    public function formatExpression(): Expression
    {
        $type = $this->getType();

        return $this->mergeExpression(new Expression(
            "matching({$type}, %format%, %value%)",
            [
                'format' => $this->format,
                'value' => $this->value,
            ],
        ));
    }

    abstract protected function getType(): string;
}
