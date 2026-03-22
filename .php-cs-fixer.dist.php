<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()->in([
    __DIR__.'/src',
    __DIR__.'/tests',
]);

// ConsoleLogger has version-specific files with incompatible syntax
if (PHP_VERSION_ID < 80000) {
    $finder->notPath('Utility/ConsoleLogger/ConsoleLogger80.php');
} else {
    $finder->notPath('Utility/ConsoleLogger/ConsoleLogger56.php');
}

return (new Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setRules([
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ]);
