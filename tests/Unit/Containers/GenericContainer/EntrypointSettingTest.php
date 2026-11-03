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
    public function testEntrypointIsProvidedByEntrypointSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(EntrypointSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testEntrypointFromStaticEntrypoint()
    {
        $container = (new EntrypointSettingWithStaticEntrypointContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommands(['-c', 'echo hello'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame("hello\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithEntrypoint()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withEntrypoint('/bin/sh')
            ->withCommands(['-c', 'echo hello'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame("hello\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testEntrypointRejectsInvalidStaticConfiguration()
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
