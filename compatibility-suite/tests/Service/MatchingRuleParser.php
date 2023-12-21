<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Exception\MatchingRuleConditionException;
use PhpPactTest\CompatibilitySuite\Model\MatchingRule;

final class MatchingRuleParser implements MatchingRuleParserInterface
{
    public function __construct(
        private MatchingRuleConverterInterface $converter,
        private FixtureLoaderInterface $fixtureLoader
    ) {
    }

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

    private function loadFromV2Map(array $map): array
    {
        $rules = [];
        foreach ($map as $k => $v) {
            if ($k === '$.body') {
                $rules[] = new MatchingRule($v['match'], 'body', '$', $v);
            } elseif (str_starts_with($k, '$.body')) {
                $rules[] = new MatchingRule($v['match'], 'body', '$' . substr($k, 6), $v);
            } elseif (str_starts_with($k, '$.headers')) {
                $rules[] = new MatchingRule($v['match'], 'header', explode('.', $k, 3)[2], $v);
            } else {
                @[, $category, $subCategory] = explode('.', $k, 3);
                $rules[] = new MatchingRule($v['match'], $category, $subCategory ?? '', $v);
            }
        }

        return $rules;
    }

    private function loadFromV3Map(array $map): array
    {
        foreach ($map as $category => $subMap) {
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

    private function getV3BodyMatchers(array $map): array
    {
        $matchers = [];
        foreach ($map as $subCategory => $subMap) {
            if ($subMap['combine'] !== 'AND') {
                throw new MatchingRuleConditionException("FFI call doesn't support OR matcher condition");
            }
            foreach ($subMap['matchers'] as $matcher) {
                switch ($matcher['match']) {
                    case 'eachKey':
                    case 'eachValue':
                        $matcher['rules'] = array_map(fn (array $rule) => $this->converter->convert(new MatchingRule($rule['match'], '', '', $rule), null), $matcher['rules']);
                        break;

                    case 'arrayContains':
                        $items = [];
                        foreach ($matcher['variants'] as $variant) {
                            $value = [];
                            foreach ($variant['rules'] as $key => $rule) {
                                $key = str_replace('$.', '', $key);
                                if ($key === '*') {
                                    // TODO It seems that IntegrationJson doesn't support '*'. Find a better way than hard coding like this.
                                    $value['href'] = $this->converter->convert(new MatchingRule($rule['matchers'][0]['match'], '', '', $rule['matchers'][0]), 'http://api.x.io/orders/42/items');
                                    $value['title'] = $this->converter->convert(new MatchingRule($rule['matchers'][0]['match'], '', '', $rule['matchers'][0]), 'Delete Item');
                                } else {
                                    $regex = str_replace('\-', '-', $rule['matchers'][0]['regex']);
                                    $value[$key] = $this->converter->convert(new MatchingRule($rule['matchers'][0]['match'], '', '', $rule['matchers'][0]), $regex);
                                }
                            }
                            $items[] = $value;
                        }
                        $matcher['variants'] = $items;
                        break;

                    default:
                        break;
                }
                $matchers[] = new MatchingRule($matcher['match'], 'body', $subCategory, $matcher);
            }
        }
        $this->sortMatchersByLevel($matchers);

        return $matchers;
    }

    private function getV4StatusCodeMatchers(array $map): array
    {
        $matcher = $map['matchers'][0];

        return [
            new MatchingRule($matcher['match'], 'status', '', $matcher),
        ];
    }

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
