<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpPactTest\CompatibilitySuite\Constant\Path;

final class SetUpContext implements Context
{
    /**
     * @BeforeScenario
     */
    public function cleanUpPacts(BeforeScenarioScope $scope): void
    {
        $files = glob(Path::PACTS_PATH . '/*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
