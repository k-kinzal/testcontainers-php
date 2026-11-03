<?php

declare(strict_types=1);

namespace Rules;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\AfterFileAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;
use Psalm\Plugin\EventHandler\Event\AfterFileAnalysisEvent;
use SimpleXMLElement;

// Psalm 4+ requires typed hook signatures. Psalm 0.3 loads AllRule56 instead.
final class AllRule implements PluginEntryPointInterface, AfterClassLikeVisitInterface, AfterFileAnalysisInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }

    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        PHPUnitReferenceRegistrar::afterClassLikeVisit($event);
    }

    public static function afterAnalyzeFile(AfterFileAnalysisEvent $event): void
    {
        PHPUnitReferenceRegistrar::afterAnalyzeFile($event);
        PHPUnitRules\AssertSelfRule::check($event->getStatementsSource(), $event->getStmts(), $event->getCodebase());
        UnitTestRules::check($event->getStatementsSource(), $event->getStmts(), $event->getCodebase());
    }
}
