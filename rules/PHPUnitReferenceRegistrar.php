<?php

namespace Rules;

use PhpParser\Node;
use PHPUnit\Framework\TestCase;
use Psalm\Checker\ClassLikeChecker;
use Psalm\Checker\FileChecker;
use Psalm\Checker\MethodChecker;
use Psalm\Internal\Analyzer\ClassLikeAnalyzer;
use Rules\UnitTestRules\UnitTestProviderRule;

final class PHPUnitReferenceRegistrar
{
    private static $legacyMethods = [];

    public static function rememberLegacy(array $statements)
    {
        foreach (UnitTestRules::nodes($statements) as $class) {
            if (!$class instanceof Node\Stmt\ClassLike || $class->name === null) {
                continue;
            }
            $name = UnitTestRules::className($class);
            $storage = UnitTestRules::classStorage($name);
            foreach ($class->getMethods() as $method) {
                $methodName = strtolower((string) $method->name);
                $comment = $method->getDocComment();
                $isTest = $method->isPublic() && (strpos((string) $method->name, 'test') === 0
                    || preg_match('/@test\b/', $comment !== null ? $comment->getText() : ''));
                self::$legacyMethods[strtolower($name).'::'.$methodName] = [
                    $isTest, UnitTestProviderRule::references($method, $storage->methods[$methodName]),
                ];
            }
        }
    }

    public static function registerLegacy(FileChecker $source)
    {
        foreach (ClassLikeChecker::getClassesForFile($source->getFilePath()) as $name) {
            $storage = UnitTestRules::classStorage($name);
            if (!UnitTestRules::extendsClass($storage, TestCase::class)) {
                continue;
            }
            ClassLikeChecker::classOrInterfaceExists($name, $source);
            foreach ($storage->declaring_method_ids as $methodName => $id) {
                $key = strtolower($id);
                if (!isset(self::$legacyMethods[$key]) || !self::$legacyMethods[$key][0]) {
                    continue;
                }
                MethodChecker::methodExists($name.'::'.$methodName, $source);
                foreach (self::$legacyMethods[$key][1] as $provider) {
                    if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $provider)) {
                        MethodChecker::methodExists($name.'::'.$provider, $source);
                    }
                }
            }
        }
    }

    public static function afterClassLikeVisit($event)
    {
        $root = rtrim($event->getCodebase()->config->base_dir, '/');
        if (strpos($event->getStatementsSource()->getFilePath(), $root.'/tests/') !== 0) {
            return;
        }
        foreach ($event->getStmt()->getMethods() as $method) {
            $storage = $event->getStorage()->methods[strtolower($method->name->toString())];
            $isTest = strpos((string) $method->name, 'test') === 0
                || preg_match('/@test\b/', $method->getDocComment() !== null ? $method->getDocComment()->getText() : '');
            foreach ($storage->attributes as $attribute) {
                $isTest = $isTest || strcasecmp($attribute->fq_class_name, 'PHPUnit\\Framework\\Attributes\\Test') === 0;
            }
            // Keep discovery metadata in Psalm's cache alongside the declaring method.
            $storage->custom_metadata[self::class] = $method->isPublic() && $isTest;
            $storage->custom_metadata[self::class.'::providers'] = UnitTestProviderRule::references($method, $storage);
        }
    }

    public static function afterAnalyzeFile($event)
    {
        $codebase = $event->getCodebase();
        $root = rtrim($codebase->config->base_dir, '/');
        if (strpos($event->getStatementsSource()->getFilePath(), $root.'/tests/') !== 0) {
            return;
        }
        foreach ($event->getFileStorage()->classlikes_in_file as $name) {
            $storage = $codebase->classlike_storage_provider->get($name);
            if (!isset($storage->parent_classes[strtolower(TestCase::class)])) {
                continue;
            }
            // These APIs record references, as in psalm/plugin-phpunit. Other members
            // remain subject to normal unused-code analysis.
            $codebase->classOrInterfaceExists($name, $storage->location);
            foreach ($storage->declaring_method_ids as $methodName => $methodId) {
                $method = $codebase->methods->getStorage($methodId);
                if ($method->visibility !== ClassLikeAnalyzer::VISIBILITY_PUBLIC || empty($method->custom_metadata[self::class])) {
                    continue;
                }
                $testId = $name.'::'.$methodName;
                $codebase->methodExists($testId, null, 'PHPUnit\\Framework\\TestSuite::run');
                // Resolve inherited tests on this class, including provider overrides.
                foreach ($method->custom_metadata[self::class.'::providers'] as $provider) {
                    if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $provider)) {
                        $codebase->methodExists($name.'::'.$provider, null, $testId);
                    }
                }
            }
        }
    }
}
