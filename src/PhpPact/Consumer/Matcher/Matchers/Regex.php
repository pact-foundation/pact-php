<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\InvalidRegexException;
use PhpPact\Consumer\Matcher\Generators\Regex as RegexGenerator;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

use function preg_last_error;
use function preg_match;

class Regex extends GeneratorAwareMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    use JsonFormattableTrait;

    /**
     * @param string|string[]|null $values
     */
    public function __construct(
        private string $regex,
        private string|array|null $values = null,
    ) {
        if ($values === null) {
            $this->setGenerator(new RegexGenerator($this->regex));
        } else {
            $this->validateRegex();
        }
        parent::__construct();
    }

    /**
     * @todo Use json_validate()
     */
    private function validateRegex(): void
    {
        foreach ((array) $this->values as $value) {
            $result = preg_match("/$this->regex/", $value);

            if ($result !== 1) {
                $errorCode = preg_last_error();

                throw new InvalidRegexException("The value '{$value}' doesn't match pattern '{$this->regex}'. Failed with error code {$errorCode}.");
            }
        }
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
