<?php

namespace Rules\UnitTestRules;

use PHPUnit\Framework\TestCase;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;
use Rules\UnitTestRules;

final class UnitTestInheritanceRule extends CodeIssue
{
    public static function check($source, array $nodes, $test, $codebase = null)
    {
        if ($test === null) {
            return;
        }

        $storage = UnitTestRules::classStorage(UnitTestRules::className($test), $codebase);
        $message = null;
        if (!UnitTestRules::extendsClass($storage, TestCase::class)) {
            $message = 'A Unit test must extend PHPUnit\\Framework\\TestCase.';
        } else {
            $framework = UnitTestRules::classStorage(TestCase::class, $codebase);
            $bases = array_diff(array_map('strtolower', $storage->parent_classes), array_map('strtolower', $framework->parent_classes), [strtolower(TestCase::class)]);
            if ($storage->abstract) {
                $bases[] = $storage->name;
            }
            foreach ($bases as $baseName) {
                $base = UnitTestRules::classStorage($baseName, $codebase);
                $sourceName = preg_replace('/^Tests\\\\Unit\\\\(.+)Test(?:Case)?$/D', 'Testcontainers\\\\$1', $base->name);
                if (!$base->abstract || !UnitTestRules::isInterface($sourceName, $source, $codebase)) {
                    $message = 'Shared abstract TestCases are allowed only for corresponding source interfaces.';
                    break;
                }
            }
        }
        if ($message !== null) {
            IssueBuffer::accepts(new self($message, new CodeLocation($source, $test->extends !== null ? $test->extends : $test)));
        }
    }
}
