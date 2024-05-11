<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ContentTypeFormatter implements JsonFormatterInterface
{
    /**
     * @return array<string, string>
     */
    public function format(MatcherInterface $matcher): array
    {
        if (!$matcher instanceof ContentType) {
            throw new MatcherNotSupportedException(sprintf('Matcher %s is not supported by %s', $matcher->getType(), self::class));
        }

        return [
            'pact:matcher:type' => $matcher->getType(),
            'value' => $matcher->getContentType(),
        ];
    }
}
