<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;

/**
 * Match binary data by its content type (magic file check)
 */
class ContentType extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    public function __construct(private string $contentType, private string $value = '')
    {
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => 'contentType',
            'value' => $this->contentType,
        ]);
    }

    public function formatExpression(): Expression
    {
        return new Expression(
            'matching(contentType, %contentType%, %value%)',
            [
                'contentType' => $this->contentType,
                'value' => $this->value,
            ]
        );
    }
}
