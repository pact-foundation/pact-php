<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ContentTypeFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof ContentType) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf("matching(contentType, %s, %s)", $this->normalize($matcher->getContentType()), $this->normalize($matcher->getValue()));
    }
}
