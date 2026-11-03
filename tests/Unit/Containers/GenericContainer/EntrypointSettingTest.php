<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\EntrypointSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

final class EntrypointSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasEntrypointSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(EntrypointSetting::class, $uses);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testStartRejectsInvalidEntrypoint()
    {
        $container = new EntrypointSettingWithInvalidEntrypointContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class EntrypointSettingWithStaticEntrypointContainer extends GenericContainer
{
    protected static $ENTRYPOINT = '/bin/sh';
}

final class EntrypointSettingWithInvalidEntrypointContainer extends GenericContainer
{
    protected static $ENTRYPOINT = [];
}
