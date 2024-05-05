<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ContentTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\ContentTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Model\ExpressionFormatterInterface;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;

/**
 * Match binary data by its content type (magic file check)
 */
class ContentType extends AbstractMatcher
{
    public function __construct(private string $contentType, private string $value = '')
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'contentType';
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function createJsonFormatter(): JsonFormatterInterface
    {
        return new JsonFormatter();
    }

    public function createExpressionFormatter(): ExpressionFormatterInterface
    {
        return new ExpressionFormatter();
    }
}
