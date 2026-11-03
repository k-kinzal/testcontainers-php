<?php

namespace Rules\UnitTestRules;

use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;

final class UnitTestNoControlFlowRule extends CodeIssue
{
    public static function check($source, array $nodes, $test, $codebase = null)
    {
        foreach ($nodes as $node) {
            if (!($node instanceof Node\Stmt\If_ || $node instanceof Node\Stmt\For_
            || $node instanceof Node\Stmt\Foreach_ || $node instanceof Node\Stmt\While_
            || $node instanceof Node\Stmt\Do_ || $node instanceof Node\Stmt\Switch_
            || $node instanceof Node\Stmt\Goto_ || $node instanceof Node\Stmt\Break_
            || $node instanceof Node\Stmt\Continue_ || $node instanceof Node\Expr\Match_
            || $node instanceof Node\Expr\Ternary || $node instanceof Node\Expr\BinaryOp\Coalesce
            || $node instanceof Node\Expr\AssignOp\Coalesce
            || $node instanceof Node\Expr\BinaryOp\BooleanAnd || $node instanceof Node\Expr\BinaryOp\BooleanOr
            || $node instanceof Node\Expr\BinaryOp\LogicalAnd || $node instanceof Node\Expr\BinaryOp\LogicalOr
            )) {
                continue;
            }
            IssueBuffer::accepts(new self(
                'Branches, loops and short-circuit expressions are forbidden in Unit tests; try/catch/finally, return, throw and yield are allowed.',
                new CodeLocation($source, $node)
            ));
        }
    }
}
