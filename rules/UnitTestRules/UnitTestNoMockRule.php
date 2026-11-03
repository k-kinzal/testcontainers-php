<?php

namespace Rules\UnitTestRules;

use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;

final class UnitTestNoMockRule extends CodeIssue
{
    private static function factory($name)
    {
        $parts = preg_split('/\\\\|::/', $name);
        $name = strtolower(end($parts));
        return preg_match('/^(?:create.*(?:mock|stub).*|getmock.*|getobjectfortrait|createtestproxy|prophesize|mock|spy|testdouble|reveal)$/D', $name) === 1;
    }

    public static function check($source, array $nodes, $test, $codebase = null)
    {
        foreach ($nodes as $node) {
            $name = null;
            if ($node instanceof Node\Expr\MethodCall || $node instanceof Node\Expr\NullsafeMethodCall
                || $node instanceof Node\Expr\StaticCall || $node instanceof Node\Expr\FuncCall
                || $node instanceof Node\Stmt\ClassMethod) {
                if (is_string($node->name)) {
                    $name = $node->name;
                } elseif ($node->name instanceof Node\Identifier || $node->name instanceof Node\Name) {
                    $name = $node->name->getAttribute('resolvedName') ?: $node->name->toString();
                } elseif ($node->name instanceof Node\Scalar\String_) {
                    $name = $node->name->value;
                }
            }
            $doubleClass = ($node instanceof Node\Expr\New_ || $node instanceof Node\Expr\StaticCall)
                && $node->class instanceof Node\Name
                && preg_match('/^(?:PHPUnit\\\\Framework\\\\MockObject\\\\|PHPUnit_Framework_MockObject_|Mockery(?:\\\\|$)|Prophecy\\\\)/i', ($node->class->getAttribute('resolvedName') ?: $node->class->toString()));
            $callableFactory = false;
            if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name
                && in_array(strtolower($node->name->toString()), ['call_user_func', 'call_user_func_array'], true)
                && isset($node->args[0])) {
                $callable = $node->args[0]->value;
                if ($callable instanceof Node\Expr\Array_ && isset($callable->items[1])) {
                    $callable = $callable->items[1]->value;
                }
                $callableFactory = $callable instanceof Node\Scalar\String_ && self::factory($callable->value);
            }
            if (($name !== null && self::factory($name)) || $doubleClass || $callableFactory) {
                IssueBuffer::accepts(new self(
                    'Mock, stub, prophecy and spy creation is forbidden in Unit tests; use real objects.',
                    new CodeLocation($source, $node)
                ));
            }
        }
    }
}
