<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Trait\GeneratorAwareTrait;

abstract class GeneratorAwareMatcher extends AbstractMatcher implements GeneratorAwareInterface
{
    use GeneratorAwareTrait;
}
