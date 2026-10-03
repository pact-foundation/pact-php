<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Exception\MatchingRuleConditionException;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;
use PhpPactTest\CompatibilitySuite\Model\MatchingRule;

final class MatchingRuleParser implements MatchingRuleParserInterface
{
    public function __construct(
        private MatchingRuleConverterInterface $converter,
        private FixtureLoaderInterface $fixtureLoader
    ) {
    }

    /**
     * @return array<int, MatchingRule>
     */
    public function parse(string $fileName): array
    {
        $map = $this->fixtureLoader->loadJson($fileName);
        switch ($this->getSpecification($fileName)) {
            case 'v2':
                return $this->loadFromV2Map($map);

            case 'v3':
            case 'v4':
                return $this->loadFromV3Map($map);

            default:
                return [];
        }
    }

    /**
     * @param array<array-key, mixed> $map
     * @return array<int, MatchingRule>
     */
    private function loadFromV2Map(array $map): array
    {
        $rules = [];
        foreach ($map as $k => $v) {
            $v = (array) $v;
            if ($k === '$.body') {
                $rules[] = new MatchingRule(TypeCaster::toString($v['match']), 'body', '$', $v);
            } elseif (str_starts_with($k, '$.body')) {
                $rules[] = new MatchingRule(TypeCaster::toString($v['match']), 'body', '$' . substr($k, 6), $v);
            } elseif (str_starts_with($k, '$.headers')) {
                $rules[] = new MatchingRule(TypeCaster::toString($v['match']), 'header', explode('.', $k, 3)[2], $v);
            } else {
                @[, $category, $subCategory] = explode('.', $k, 3);
                $rules[] = new MatchingRule(TypeCaster::toString($v['match']), TypeCaster::toString($category), TypeCaster::toString($subCategory), $v);
            }
        }

        return $rules;
    }

    /**
     * @param array<array-key, mixed> $map
     * @return array<int, MatchingRule>
     */
    private function loadFromV3Map(array $map): array
    {
        foreach ($map as $category => $subMap) {
            $subMap = (array) $subMap;
            switch ($category) {
                case 'body':
                    return $this->getV3BodyMatchers($subMap);

                case 'status':
                    return $this->getV4StatusCodeMatchers($subMap);

                default:
                    break;
            }
        }

        return [];
    }

    /**
     * @param array<array-key, mixed> $map
     * @return array<int, MatchingRule>
     */
    private function getV3BodyMatchers(array $map): array
    {
        $matchers = [];
        foreach ($map as $subCategory => $subMap) {
            $subMap = (array) $subMap;
            if ($subMap['combine'] !== 'AND') {
                throw new MatchingRuleConditionException("FFI call doesn't support OR matcher condition");
            }
            foreach ((array) $subMap['matchers'] as $matcher) {
                $matcher = (array) $matcher;
                switch ($matcher['match']) {
                    case 'eachKey':
                    case 'eachValue':
                        $rules = [];
                        foreach ((array) $matcher['rules'] as $rule) {
                            $rule = (array) $rule;
                            $rules[] = $this->converter->convert(new MatchingRule(TypeCaster::toString($rule['match']), '', '', $rule), null);
                        }
                        $matcher['rules'] = $rules;
                        break;

                    case 'arrayContains':
                        $items = [];
                        foreach ((array) $matcher['variants'] as $variant) {
                            $variant = (array) $variant;
                            $value = [];
                            foreach ((array) $variant['rules'] as $key => $rule) {
                                $key = str_replace('$.', '', TypeCaster::toString($key));
                                $firstMatcher = (array) ((array) ((array) $rule)['matchers'])[0];
                                if ($key === '*') {
                                    // TODO It seems that IntegrationJson doesn't support '*'. Find a better way than hard coding like this.
                                    $value['href'] = $this->converter->convert(new MatchingRule(TypeCaster::toString($firstMatcher['match']), '', '', $firstMatcher), 'http://api.x.io/orders/42/items');
                                    $value['title'] = $this->converter->convert(new MatchingRule(TypeCaster::toString($firstMatcher['match']), '', '', $firstMatcher), 'Delete Item');
                                } else {
                                    $regex = str_replace('\-', '-', TypeCaster::toString($firstMatcher['regex']));
                                    $value[$key] = $this->converter->convert(new MatchingRule(TypeCaster::toString($firstMatcher['match']), '', '', $firstMatcher), $regex);
                                }
                            }
                            $items[] = $value;
                        }
                        $matcher['variants'] = $items;
                        break;

                    default:
                        break;
                }
                $matchers[] = new MatchingRule(TypeCaster::toString($matcher['match']), 'body', TypeCaster::toString($subCategory), $matcher);
            }
        }
        $this->sortMatchersByLevel($matchers);

        return $matchers;
    }

    /**
     * @param array<array-key, mixed> $map
     * @return array<int, MatchingRule>
     */
    private function getV4StatusCodeMatchers(array $map): array
    {
        $matcher = (array) ((array) $map['matchers'])[0];

        return [
            new MatchingRule(TypeCaster::toString($matcher['match']), 'status', '', $matcher),
        ];
    }

    /**
     * @param array<int, MatchingRule> $matchers
     */
    private function sortMatchersByLevel(array &$matchers): void
    {
        usort(
            $matchers,
            fn (MatchingRule $a, MatchingRule $b) => count(explode('.', $b->getSubCategory())) - count(explode('.', $a->getSubCategory()))
        );
    }

    private function getSpecification(string $fileName): string
    {
        $basename = substr_replace($fileName, '', -5);
        $parts = explode('-', $basename);

        return end($parts);
    }
}
