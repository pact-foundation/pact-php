<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\Generator;

final class GeneratorParser implements GeneratorParserInterface
{
    public function __construct(
        private FixtureLoaderInterface $fixtureLoader
    ) {
    }

    public function parse(string $value): array
    {
        if (str_starts_with($value, 'JSON:')) {
            $value = substr($value, 5);
            $map = json_decode($value, true);
        } else {
            $map = $this->fixtureLoader->loadJson($value);
        }

        return $this->loadFromMap($map);
    }

    private function loadFromMap(array $map): array
    {
        $generators = [];
        $removeType = fn (array $values): array => array_filter(
            $values,
            fn (mixed $v, string $k) => $k !== 'type',
            ARRAY_FILTER_USE_BOTH
        );
        foreach ($map as $category => $values) {
            switch ($category) {
                case 'path':
                case 'method':
                case 'status':
                    $generators[] = new Generator($values['type'], $category, null, $removeType($values));
                    break;

                default:
                    foreach ($values as $subCategory => $value) {
                        $generators[] = new Generator($value['type'], $category, $subCategory, $removeType($value));
                    }
                    break;
            }
        }

        return $generators;
    }
}
