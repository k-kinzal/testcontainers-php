<?php

namespace Rules\UnitTestRules;

use PHPUnit\Framework\TestCase;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;
use ReflectionClass;
use Rules\UnitTestRules;

final class UnitTestMethodRule extends CodeIssue
{
    public static function check($source, array $nodes, $test, $codebase = null)
    {
        if ($test === null || !preg_match('/^Tests\\\\Unit\\\\(.+)Test(?:Case)?$/D', UnitTestRules::className($test), $match)) {
            return;
        }
        $sourceName = 'Testcontainers\\'.$match[1];
        if (UnitTestRules::classStorage($sourceName, $codebase) === null) {
            return;
        }
        $subject = UnitTestRules::classStorage($sourceName, $codebase);
        $framework = new ReflectionClass(TestCase::class);
        foreach ($test->getMethods() as $method) {
            $name = (string) $method->name;
            if ($framework->hasMethod($name)) {
                $override = $framework->getMethod($name);
                if (!$override->isPrivate() && !$override->isFinal()) {
                    continue;
                }
            }
            if (preg_match('/^provider[A-Z][A-Za-z0-9]*$/D', $name) && $method->isPublic() && $method->isStatic()) {
                continue;
            }
            if ($method->isPublic() && !$method->isStatic() && preg_match('/^test[A-Z][A-Za-z0-9]*$/D', $name)) {
                $suffix = substr($name, 4);
                foreach (array_keys($subject->declaring_method_ids) as $sourceMethod) {
                    // Magic methods and CONSTANT_STYLE factories use PascalCase in test names.
                    $prefix = str_replace('_', '', $sourceMethod);
                    if ($prefix !== '' && strncasecmp($suffix, $prefix, strlen($prefix)) === 0
                        && (strlen($suffix) === strlen($prefix) || ctype_upper($suffix[strlen($prefix)]))) {
                        continue 2;
                    }
                }
            }
            IssueBuffer::accepts(new self(
                $name.' must be test[ExistingSourceMethod][Behavior], a public static provider[TestCaseName], or a PHPUnit TestCase override.',
                new CodeLocation($source, $method)
            ));
        }
    }
}
