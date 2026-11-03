<?php

namespace Rules\UnitTestRules;

use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;
use Rules\UnitTestRules;
use Testcontainers\Containers\GenericContainer\GenericContainer;

final class UnitTestHelperClassRule extends CodeIssue
{
    public static function check($source, array $nodes, $test, $codebase = null)
    {
        foreach ($nodes as $class) {
            if (!$class instanceof Node\Stmt\ClassLike || $class === $test) {
                continue;
            }
            if ($class instanceof Node\Stmt\Class_ && $class->extends !== null) {
                $parent = $class->extends->getAttribute('resolvedName') ?: $class->extends->toString();
                if (strcasecmp($parent, GenericContainer::class) === 0
                    || UnitTestRules::extendsClass(UnitTestRules::classStorage($parent, $codebase), GenericContainer::class)) {
                    continue;
                }
            }
            IssueBuffer::accepts(new self(
                'Additional declarations in Unit test files must be GenericContainer subclasses.',
                new CodeLocation($source, $class)
            ));
        }
    }
}
