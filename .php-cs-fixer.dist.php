<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()->in([
    __DIR__.'/src',
    __DIR__.'/tests',
]);

// ConsoleLogger requires PHP 8.0+ (uses string|\Stringable union type from psr/log 3.x)
if (PHP_VERSION_ID < 80000) {
    $finder->notPath('Utility/ConsoleLogger.php');
    $finder->notPath('Utility/ConsoleLoggerTest.php');
}

return (new Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setRules([
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ]);
