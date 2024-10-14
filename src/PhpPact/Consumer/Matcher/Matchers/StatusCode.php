<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * Matches the response status code.
 */
class StatusCode extends GeneratorAwareMatcher implements JsonFormattableInterface
{
    use JsonFormattableTrait;

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

    public function formatJson(): Attributes
    {
        return $this->mergeJson(new Attributes([
            'pact:matcher:type' => 'statusCode',
            'status' => $this->status->value,
            'value' => $this->value,
        ]));
    }
}
