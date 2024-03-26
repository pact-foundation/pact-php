<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4;

use Behat\Behat\Context\Context;
use PhpPactTest\CompatibilitySuite\Service\BodyValidatorInterface;

final class BodyGeneratorsContext implements Context
{
    public function __construct(
        private BodyValidatorInterface $validator,
    ) {
    }

    /**
     * @Then the body value for :path will have been replaced with :value
     */
    public function theBodyValueForWillHaveBeenReplacedWith(string $path, string $value): void
    {
        $this->validator->validateValue($path, $value);
    }
}
