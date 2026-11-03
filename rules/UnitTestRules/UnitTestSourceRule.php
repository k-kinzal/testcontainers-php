<?php

namespace Rules\UnitTestRules;

use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;
use Rules\UnitTestRules;

final class UnitTestSourceRule extends CodeIssue
{
    public static function check($source, array $nodes, $test, $codebase = null)
    {
        $root = dirname(dirname(__DIR__));
        $path = $source->getFilePath();

        $name = $test !== null ? UnitTestRules::className($test) : '';
        $node = $test !== null ? $test : (isset($nodes[0]) ? $nodes[0] : new Node\Stmt\Nop([
            'startLine' => 1, 'endLine' => 1, 'startFilePos' => 0, 'endFilePos' => 0,
        ]));
        $message = null;
        if (!preg_match('/^Tests\\\\Unit\\\\(.+)Test(?:Case)?$/D', $name, $match)) {
            $message = 'A Unit file must declare its matching Tests\\Unit\\...Test class (or an abstract ...TestCase for an interface).';
        } else {
            $sourceName = 'Testcontainers\\'.$match[1];
            $relative = str_replace('\\', '/', $match[1]);
            $subject = UnitTestRules::classStorage($sourceName, $codebase);
            $expectedTest = $root.'/tests/Unit/'.str_replace('\\', '/', substr($name, strlen('Tests\\Unit\\'))).'.php';
            if ($path !== $expectedTest || $subject === null || $subject->location === null
                || $subject->location->file_path !== $root.'/src/'.$relative.'.php') {
                $message = 'Unit test must mirror '.$sourceName.' in src/'.$relative.'.php, including its namespace and file path.';
            } elseif (substr($name, -8) === 'TestCase' && (!$test->isAbstract() || !UnitTestRules::isInterface($sourceName, $source, $codebase))) {
                $message = 'A shared ...TestCase must be abstract and correspond to a source interface.';
            }
        }
        if ($message !== null) {
            IssueBuffer::accepts(new self($message, new CodeLocation($source, $node)));
        }
    }
}
