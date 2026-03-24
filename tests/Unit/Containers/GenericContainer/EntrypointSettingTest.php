<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\EntrypointSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
class EntrypointSettingTest extends TestCase
{
    public function testHasEntrypointSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(EntrypointSetting::class, $uses);
    }

    public function testStaticEntrypoint()
    {
        $container = (new EntrypointSettingWithStaticEntrypointContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommands(['-c', 'echo hello'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("hello\n", $instance->getOutput());
    }

    public function testStartWithEntrypoint()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withEntrypoint('/bin/sh')
            ->withCommands(['-c', 'echo hello'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("hello\n", $instance->getOutput());
    }
}

class EntrypointSettingWithStaticEntrypointContainer extends GenericContainer
{
    protected static $ENTRYPOINT = '/bin/sh';
}
