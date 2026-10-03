<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\Generator;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

final class GeneratorParser implements GeneratorParserInterface
{
    public function __construct(
        private FixtureLoaderInterface $fixtureLoader
    ) {
    }

    /**
     * @return array<int, Generator>
     */
    public function parse(string $value): array
    {
        if (str_starts_with($value, 'JSON:')) {
            $value = substr($value, 5);
            $map = json_decode($value, true);
            if (!is_array($map)) {
                $map = [];
            }
        } else {
            $map = $this->fixtureLoader->loadJson($value);
        }

        return $this->loadFromMap($map);
    }

    /**
     * @param array<array-key, mixed> $map
     * @return array<int, Generator>
     */
    private function loadFromMap(array $map): array
    {
        $generators = [];
        $removeType = fn (array $values): array => array_filter(
            $values,
            fn (mixed $v, string|int $k) => $k !== 'type',
            ARRAY_FILTER_USE_BOTH
        );
        foreach ($map as $category => $values) {
            $values = (array) $values;
            switch ($category) {
                case 'path':
                case 'method':
                case 'status':
                    $generators[] = new Generator(TypeCaster::toString($values['type']), TypeCaster::toString($category), null, $removeType($values));
                    break;

                default:
                    foreach ($values as $subCategory => $value) {
                        $value = (array) $value;
                        $generators[] = new Generator(TypeCaster::toString($value['type']), TypeCaster::toString($category), TypeCaster::toString($subCategory), $removeType($value));
                    }
                    break;
            }
        }

        return $generators;
    }
}
