<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Exception\GeneratorNotRequiredException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Matchers\AbstractDateTime;
use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\MatchingField;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class PluginFormatter implements FormatterInterface
{
    public const MATCHERS_WITHOUT_CONFIG = ['equality', 'type', 'number', 'integer', 'decimal', 'include', 'boolean', 'semver'];

    public function format(MatcherInterface $matcher): string
    {
        if ($matcher instanceof GeneratorAwareInterface && null !== $matcher->getGenerator()) {
            throw new GeneratorNotRequiredException('Generator is not support in plugin');
        }

        if ($matcher instanceof NullValue) {
            return $this->formatMatchersWithoutConfig(new Type(null));
        }

        if (in_array($matcher->getType(), self::MATCHERS_WITHOUT_CONFIG)) {
            return $this->formatMatchersWithoutConfig($matcher);
        }
        if ($matcher instanceof AbstractDateTime || $matcher instanceof Regex || $matcher instanceof ContentType) {
            return $this->formatMatchersWithConfig($matcher);
        }

        throw new MatcherNotSupportedException(sprintf("Matcher '%s' is not supported by plugin", $matcher->getType()));
    }

    public function formatMatchingFieldMatcher(MatchingField $matcher): string
    {
        return sprintf("matching($%s)", $this->normalize($matcher->getFieldName()));
    }

    private function formatMatchersWithoutConfig(MatcherInterface $matcher): string
    {
        $type = $matcher->getType() === 'equality' ? 'equalTo' : $matcher->getType();

        return sprintf('matching(%s, %s)', $type, $this->normalize($matcher->getValue()));
    }

    private function formatMatchersWithConfig(AbstractDateTime|Regex|ContentType $matcher): string
    {
        $config = match (true) {
            $matcher instanceof AbstractDateTime => $matcher->getFormat(),
            $matcher instanceof Regex => $matcher->getRegex(),
            $matcher instanceof ContentType => $matcher->getValue(),
        };

        return sprintf("matching(%s, %s, %s)", $matcher->getType(), $this->normalize($config), $this->normalize($matcher->getValue()));
    }

    public function formatNotEmptyMatcher(NotEmpty $matcher): string
    {
        return sprintf('notEmpty(%s)', $this->normalize($matcher->getValue()));
    }

    private function formatMapMatchers(EachKey|EachValue $matcher): string
    {
        $rules = $matcher->getRules();
        if (count($rules) === 0 || count($rules) > 1) {
            throw new MatchingExpressionException(sprintf("Matcher '%s' only support 1 rule, %d provided", $matcher->getType(), count($rules)));
        }
        $rule = reset($rules);

        return sprintf('%s(%s)', $matcher->getType(), $this->format($rule));
    }

    public function formatEachKeyMatcher(EachKey $matcher): string
    {
        return $this->formatMapMatchers($matcher);
    }

    public function formatEachValueMatcher(EachValue $matcher): string
    {
        return $this->formatMapMatchers($matcher);
    }

    private function normalize(mixed $value): string
    {
        return match (gettype($value)) {
            'string' => sprintf("'%s'", str_replace("'", "\\'", $value)),
            'boolean' => $value ? 'true' : 'false',
            'integer' => (string) $value,
            'double' => (string) $value,
            'NULL' => 'null',
            default => throw new MatchingExpressionException(sprintf("Plugin formatter doesn't support value of type %s", gettype($value))),
        };
    }
}
