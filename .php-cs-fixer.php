<?php
$finder = PhpCsFixer\Finder::create();
$finder->files()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/example')
    ->in(__DIR__ . '/compatibility-suite/tests')
    ->exclude('library/src')
    ->name('*.php');

$config = new PhpCsFixer\Config();
$config->setRules([
        '@PSR12' => true,
        'strict_param' => false,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);

return $config;