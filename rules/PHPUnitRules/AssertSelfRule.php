<?php

namespace Rules\PHPUnitRules;

use PhpParser\Node;
use PHPUnit\Framework\TestCase;
use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;
use Psalm\IssueBuffer;
use ReflectionClass;
use Rules\UnitTestRules;

final class AssertSelfRule extends CodeIssue
{
    public static function check($source, array $statements, $codebase = null)
    {
        if (strpos($source->getFilePath(), dirname(dirname(__DIR__)).'/tests/') !== 0) {
            return;
        }
        $assertions = [];
        foreach ((new ReflectionClass(TestCase::class))->getMethods() as $method) {
            if ($method->isPublic() && $method->isStatic() && stripos($method->getName(), 'assert') === 0) {
                $assertions[strtolower($method->getName())] = true;
            }
        }
        foreach (UnitTestRules::nodes($statements) as $class) {
            if (!$class instanceof Node\Stmt\Class_ || $class->name === null) {
                continue;
            }
            $storage = UnitTestRules::classStorage(UnitTestRules::className($class), $codebase);
            if (!UnitTestRules::extendsClass($storage, TestCase::class)) {
                continue;
            }
            foreach ($class->getMethods() as $method) {
                self::checkNodes($source, $method->stmts === null ? [] : $method->stmts, $assertions, $codebase);
            }
        }
    }

    private static function checkNodes($source, array $nodes, array $assertions, $codebase)
    {
        foreach ($nodes as $node) {
            // A nested class has its own $this and self; it is checked separately.
            if (!$node instanceof Node || $node instanceof Node\Stmt\ClassLike) {
                continue;
            }
            if ($node instanceof Node\Expr\MethodCall || $node instanceof Node\Expr\NullsafeMethodCall
                || $node instanceof Node\Expr\StaticCall) {
                $name = null;
                if (is_string($node->name)) {
                    $name = $node->name;
                } elseif ($node->name instanceof Node\Identifier) {
                    $name = $node->name->toString();
                } elseif ($node->name instanceof Node\Scalar\String_) {
                    $name = $node->name->value;
                }
                if ($name !== null && isset($assertions[strtolower($name)]) && self::requiresSelf($node, $codebase)) {
                    IssueBuffer::accepts(new self(
                        'PHPUnit static assertions must be called with self::'.$name.'().',
                        new CodeLocation($source, $node)
                    ));
                }
            }
            foreach ($node->getSubNodeNames() as $property) {
                $value = $node->$property;
                self::checkNodes($source, is_array($value) ? $value : [$value], $assertions, $codebase);
            }
        }
    }

    private static function requiresSelf($node, $codebase)
    {
        if (!$node instanceof Node\Expr\StaticCall) {
            return $node->var instanceof Node\Expr\Variable && $node->var->name === 'this';
        }
        if (!$node->class instanceof Node\Name || strcasecmp($node->class->toString(), 'self') === 0) {
            return false;
        }
        $class = $node->class->getAttribute('resolvedName') ?: $node->class->toString();
        if (in_array(strtolower($class), [
            'static', 'parent', 'phpunit\\framework\\assert', 'phpunit\\framework\\testcase',
            'phpunit_framework_assert', 'phpunit_framework_testcase',
        ], true)) {
            return true;
        }
        return UnitTestRules::extendsClass(UnitTestRules::classStorage($class, $codebase), TestCase::class);
    }
}
