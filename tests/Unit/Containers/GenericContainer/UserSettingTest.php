<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\UserSetting;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
final class UserSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasUserSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(UserSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticUser()
    {
        $container = (new UserSettingWithStaticUserContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommands(['whoami'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("nobody\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testStartWithUser()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withUser('nobody')
            ->withCommands(['whoami'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("nobody\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidUser()
    {
        $container = new UserSettingWithInvalidUserContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class UserSettingWithStaticUserContainer extends GenericContainer
{
    protected static $USER = 'nobody';
}

final class UserSettingWithInvalidUserContainer extends GenericContainer
{
    protected static $USER = [];
}
