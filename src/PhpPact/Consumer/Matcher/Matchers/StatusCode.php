<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Matches the response status code.
 */
class StatusCode extends GeneratorAwareMatcher
{
    private HttpStatus $status;

    public function __construct(string|HttpStatus $status, private ?int $value = null)
    {
        if (is_string($status)) {
            try {
                $status = HttpStatus::from($status);
            } catch (\Throwable $th) {
                $all = implode(', ', array_map(
                    fn (HttpStatus $status) => $status->value,
                    HttpStatus::cases()
                ));
                throw new InvalidHttpStatusException(sprintf("Status '%s' is not supported. Supported status are: %s", $status, $all));
            }
        }
        $this->status = $status;

        if ($value === null) {
            $range = $status->range();

            $this->setGenerator(new RandomInt($range->min, $range->max));
        }
        parent::__construct();
    }

    public function getType(): string
    {
        return 'statusCode';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return ['status' => $this->status->value];
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new HasGeneratorFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        throw new MatcherNotSupportedException("StatusCode matcher doesn't support expression formatter");
    }
}
