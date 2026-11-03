<?php

namespace Rules;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Psalm\Checker\FileChecker;
use Psalm\Context;

// Psalm 0.3 has no file-analysis event. Its filetype handler supplies that hook.
final class AllRule56 extends FileChecker
{
    public function visit(Context $file_context = null)
    {
        parent::visit($file_context);
        if (strpos($this->getFilePath(), dirname(__DIR__).'/tests/') === 0) {
            PHPUnitReferenceRegistrar::rememberLegacy($this->resolvedStatements());
        }
    }

    public function analyze($update_docblocks = false, $preserve_checkers = false)
    {
        parent::analyze($update_docblocks, $preserve_checkers);
        if (strpos($this->getFilePath(), dirname(__DIR__).'/tests/') === 0) {
            PHPUnitReferenceRegistrar::registerLegacy($this);
        }
    }

    private function resolvedStatements()
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        return $traverser->traverse($this->getStatements());
    }
}
