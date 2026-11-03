<?php

namespace Rules\UnitTestRules;

use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\Internal\Analyzer\ClassLikeAnalyzer;
use Psalm\IssueBuffer;
use Psalm\Issue\CodeIssue;
use Psalm\Storage\MethodStorage;
use Psalm\Type\Union;
use Rules\UnitTestRules;

final class UnitTestProviderRule extends CodeIssue
{
    /** @return string[] */
    public static function references(Node\Stmt\ClassMethod $method, MethodStorage $storage)
    {
        $comment = $method->getDocComment();
        preg_match_all('/@dataProvider\s+(\S+)/', $comment !== null ? $comment->getText() : '', $matches);
        $references = $matches[1];
        foreach (isset($storage->attributes) ? $storage->attributes : [] as $attribute) {
            $value = isset($attribute->args[0]) ? $attribute->args[0]->type : null;
            if (strcasecmp($attribute->fq_class_name, 'PHPUnit\\Framework\\Attributes\\DataProvider') === 0
                && $value instanceof Union && $value->isSingleStringLiteral()) {
                $references[] = $value->getSingleStringLiteral()->value;
            }
        }
        return $references;
    }

    public static function check($source, array $nodes, $test, $codebase = null)
    {
        if ($test === null) {
            return;
        }
        $storage = UnitTestRules::classStorage(UnitTestRules::className($test), $codebase);
        $public = $codebase !== null ? ClassLikeAnalyzer::VISIBILITY_PUBLIC : \Psalm\Checker\ClassLikeChecker::VISIBILITY_PUBLIC;
        foreach ($test->getMethods() as $method) {
            $name = (string) $method->name;
            $messages = [];
            if (strpos($name, 'provider') === 0) {
                $target = 'test'.substr($name, 8);
                $targetId = isset($storage->declaring_method_ids[strtolower($target)]) ? $storage->declaring_method_ids[strtolower($target)] : null;
                $targetMethod = $targetId === null ? null : UnitTestRules::methodStorage($targetId, $codebase);
                if ($targetMethod === null || $targetMethod->cased_name !== $target
                    || $targetMethod->visibility !== $public || $targetMethod->is_static) {
                    $messages[] = $name.' requires a matching '.$target.' test method, declared here or inherited.';
                }
                if (!$method->isPublic() || !$method->isStatic() || $method->params !== []) {
                    $messages[] = 'A data provider must be public, static and parameterless.';
                }
            }
            foreach (self::references($method, $storage->methods[strtolower($name)]) as $provider) {
                $expected = 'provider'.substr($name, 4);
                if (strpos($name, 'test') !== 0 || $provider !== $expected || !isset($storage->declaring_method_ids[strtolower($provider)])) {
                    $messages[] = $name.' must reference its existing '.$expected.' provider.';
                }
            }
            foreach ($messages as $message) {
                IssueBuffer::accepts(new self($message, new CodeLocation($source, $method)));
            }
        }
    }
}
