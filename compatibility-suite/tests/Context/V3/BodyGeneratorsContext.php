<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3;

use Behat\Behat\Context\Context;
use PhpPactTest\CompatibilitySuite\Service\BodyValidatorInterface;

final class BodyGeneratorsContext implements Context
{
    public function __construct(
        private BodyValidatorInterface $validator,
    ) {
    }

    /**
     * @Then the body value for :path will have been replaced with a(n) :type
     */
    public function theBodyValueForWillHaveBeenReplacedWithA(string $path, string $type): void
    {
        $this->validator->validateType($path, $type);
    }
}
