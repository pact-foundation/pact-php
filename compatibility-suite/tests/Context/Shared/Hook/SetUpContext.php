<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\BeforeScenario;
use Behat\Hook\BeforeSuite;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PHPUnit\TextUI\Configuration\Builder;

final class SetUpContext implements Context
{
    #[BeforeScenario]
    public function cleanUpPacts(BeforeScenarioScope $scope): void
    {
        $files = glob(Path::PACTS_PATH . '/*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    #[BeforeSuite]
    public static function initPhpunit()
    {
        static $initialized = false;
        if (!$initialized) {
            (new Builder())->build([]);
            $initialized = true;
        }
    }
}
