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
}
