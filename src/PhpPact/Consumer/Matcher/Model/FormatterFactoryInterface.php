<?php

namespace PhpPact\Consumer\Matcher\Model;

interface FormatterFactoryInterface
{
    public function createExpressionFormatter(): ExpressionFormatterInterface;

    public function createJsonFormatter(): JsonFormatterInterface;
}
