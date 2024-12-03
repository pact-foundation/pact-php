<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

class Regex extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;

    /**
     * @param string|string[] $values
     */
    public function __construct(
        private string $regex,
        private string|array $values = '',
    ) {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'regex',
            'regex' => $this->regex,
            'value' => $this->values,
        ]));
    }

    public function formatExpression(): Expression
    {
        $value = $this->values;
        if (!is_string($value)) {
            throw new InvalidValueException(sprintf("Regex matching expression doesn't support value of type %s", gettype($value)));
        }

        return new Expression("matching(regex, %regex%, %value%)", ['regex' => $this->regex, 'value' => $value]);
    }
}
