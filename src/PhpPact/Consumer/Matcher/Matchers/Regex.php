<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidRegexException;
use PhpPact\Consumer\Matcher\Formatters\Expression\RegexFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex as RegexGenerator;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

use function preg_last_error;
use function preg_match;

class Regex extends GeneratorAwareMatcher
{
    /**
     * @param string|string[]|null $values
     */
    public function __construct(
        private string $regex,
        protected string|array|null $values = null,
    ) {
        if ($values === null) {
            $this->setGenerator(new RegexGenerator($this->regex));
        }
        parent::__construct();
    }

    /**
     * @return string|array<string, mixed>
     */
    public function jsonSerialize(): string|array
    {
        if (null !== $this->values) {
            $this->validateRegex();
        }

        return parent::jsonSerialize();
    }

    private function validateRegex(): void
    {
        foreach ((array) $this->values as $value) {
            $result = preg_match("/$this->regex/", $value);

            if ($result === false || $result === 0) {
                $errorCode = preg_last_error();

                throw new InvalidRegexException("The pattern '{$this->regex}' is not valid for value '{$value}'. Failed with error code {$errorCode}.");
            }
        }
    }

    public function getType(): string
    {
        return 'regex';
    }

    /**
     * @return string|string[]|null
     */
    public function getValue(): string|array|null
    {
        return $this->values;
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return ['regex' => $this->regex];
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new HasGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new RegexFormatter();
    }
}
