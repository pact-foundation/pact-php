<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Trait\JsonFormattableTrait;

/**
 * Matches the response status code.
 */
class StatusCode extends GeneratorAwareMatcher implements JsonFormattableInterface
{
    use JsonFormattableTrait;

    public function __construct(private HttpStatus $status, private int $value = 0)
    {
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
