<?php

namespace PhpPact\Standalone\Runner;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Wrapper around Process with backwards-compatibility support.
 * Can be replaced with usual Process as soon as symfony 2.x support is dropped
 *
 * @see https://symfony.com/doc/current/components/process.html#process-signals for POSIX workaround
 * @see https://github.com/symfony/symfony/pull/21474 how it works in symfony/process ^3.3
 * @see Process
 */
class ProcessRunner
{
    public static function run(string $command, array $arguments): Process
    {
        // ProcessBuilder is deprecated in symfony/process version 3.4,
        // but Process doesn't accept an array of arguments in versions <3.3,
        // in which ProcessUtils::escapeArgument() is not marked as deprecated
        $useProcess = true;
        if (\method_exists('Symfony\Component\Process\ProcessUtils', 'escapeArgument')) {
            $r = new \ReflectionMethod('Symfony\Component\Process\ProcessUtils', 'escapeArgument');
            if ($r->isPublic() && false === \strpos($r->getDocComment(), '@deprecated')) {
                $useProcess = false;
            }
        }
        if ($useProcess) {
            $process = new Process(\array_merge([$command], $arguments));
        } else {
            $pb      = new ProcessBuilder(\array_merge([$command], $arguments));
            //see https://symfony.com/doc/current/components/process.html#process-signals
            if (self::isPosixPlatform()) {
                $pb->setPrefix('exec');
            }
            $process = $pb->getProcess();
        }

        return $process;
    }

    private static function isPosixPlatform(): bool
    {
        return DIRECTORY_SEPARATOR === '/';
    }
}
