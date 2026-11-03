<?php

namespace Rules;

use PhpParser\Node;
use Psalm\Checker\ClassLikeChecker;
use Psalm\Checker\MethodChecker;

final class UnitTestRules
{
    public static function check($source, array $statements, $codebase = null)
    {
        $path = $source->getFilePath();
        $root = dirname(__DIR__);
        if (strpos($path, $root.'/tests/') !== 0) {
            return;
        }
        $nodes = self::nodes($statements);
        $isUnit = strpos($path, $root.'/tests/Unit/') === 0;
        $test = null;
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Namespace_) {
                $namespace = (string) $node->name;
                $isUnit = $isUnit || $namespace === 'Tests\\Unit' || strpos($namespace, 'Tests\\Unit\\') === 0;
            }
            if ($node instanceof Node\Stmt\Class_ && (string) $node->name === basename($path, '.php')) {
                $test = $node;
            }
        }
        if (!$isUnit) {
            return;
        }
        foreach ([
            UnitTestRules\UnitTestSourceRule::class,
        ] as $rule) {
            $rule::check($source, $nodes, $test, $codebase);
        }
    }

    /** @return Node[] */
    public static function nodes(array $nodes)
    {
        $result = [];
        foreach ($nodes as $node) {
            if (!$node instanceof Node) {
                continue;
            }
            $result[] = $node;
            foreach ($node->getSubNodeNames() as $property) {
                $value = $node->$property;
                $result = array_merge($result, self::nodes(is_array($value) ? $value : [$value]));
            }
        }
        return $result;
    }

    public static function className(Node\Stmt\ClassLike $class)
    {
        $name = isset($class->namespacedName) ? $class->namespacedName : $class->getAttribute('namespacedName');
        return (string) $name;
    }

    public static function classStorage($name, $codebase = null)
    {
        if ($codebase !== null) {
            return $codebase->classlike_storage_provider->has($name)
                ? $codebase->classlike_storage_provider->get($name) : null;
        }
        return isset(ClassLikeChecker::$storage[strtolower($name)]) ? ClassLikeChecker::$storage[strtolower($name)] : null;
    }

    public static function methodStorage($id, $codebase = null)
    {
        return $codebase !== null ? $codebase->methods->getStorage($id) : MethodChecker::getStorage($id);
    }

    public static function isInterface($name, $source, $codebase = null)
    {
        $storage = self::classStorage($name, $codebase);
        return $storage !== null && ($codebase !== null
            ? $storage->is_interface : $source->project_checker->hasFullyQualifiedInterfaceName($name));
    }

    public static function extendsClass($storage, $parent)
    {
        return $storage !== null && in_array(strtolower($parent), array_map('strtolower', $storage->parent_classes), true);
    }
}
