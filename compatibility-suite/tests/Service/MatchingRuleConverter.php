<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Matchers\ArrayContains;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Matchers\Decimal;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\Equality;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Number;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Matchers\Values;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPactTest\CompatibilitySuite\Model\MatchingRule;

final class MatchingRuleConverter implements MatchingRuleConverterInterface
{
    public function convert(MatchingRule $rule, mixed $value): ?MatcherInterface
    {
        switch ($rule->getMatcher()) {
            case 'type':
                $min = $rule->getMatcherAttribute('min');
                $max = $rule->getMatcherAttribute('max');
                if (null !== $min && null !== $max && is_array($value)) {
                    return new MinMaxType(reset($value), $min, $max);
                }
                if (null !== $min && is_array($value)) {
                    return new MinType(reset($value), $min);
                }
                if (null !== $max && is_array($value)) {
                    return new MaxType(reset($value), $max);
                }
                return new Type($value);

            case 'equality':
                return new Equality($value);

            case 'include':
                return new Includes($rule->getMatcherAttribute('value'));

            case 'number':
                return new Number($this->getNumber($value));

            case 'integer':
                return new Integer($this->getInteger($value));

            case 'decimal':
                return new Decimal($this->getDecimal($value));

            case 'null':
                return new NullValue();

            case 'date':
                return new Date($rule->getMatcherAttribute('format'), $value);

            case 'boolean':
                return new Boolean($value);

            case 'contentType':
                return new ContentType($rule->getMatcherAttribute('value'));

            case 'values':
                return new Values($value);

            case 'notEmpty':
                return new NotEmpty($value);

            case 'semver':
                return new Semver($value);

            case 'eachKey':
                return new EachKey($value, $rule->getMatcherAttribute('rules'));

            case 'eachValue':
                return new EachValue($value, $rule->getMatcherAttribute('rules'));

            case 'arrayContains':
                return new ArrayContains($rule->getMatcherAttribute('variants'));

            case 'regex':
                $regex = $rule->getMatcherAttribute('regex');
                return new Regex($regex, $value ?? '');

            case 'statusCode':
                return new StatusCode($this->getHttpStatus($rule));

            default:
                return null;
        }
    }

    private function getNumber(mixed $value): int|float
    {
        if (is_numeric($value)) {
            return $value + 0;
        }

        // @todo Fix this compatibility-suite's mistake: there is no number in `basic.json`
        return 1;
    }

    private function getInteger(mixed $value): int
    {
        if (is_numeric($value)) {
            return $value + 0;
        }

        // @todo Fix this compatibility-suite's mistake: there is no integer in `basic.json`
        return 1;
    }

    private function getDecimal(mixed $value): float
    {
        if (is_numeric($value)) {
            return $value + 0;
        }

        // @todo Fix this compatibility-suite's mistake: there is no decimal in `basic.json`
        return 1.1;
    }

    private function getHttpStatus(MatchingRule $rule): HttpStatus
    {
        return HttpStatus::from($rule->getMatcherAttribute('status') ?? '');
    }
}
