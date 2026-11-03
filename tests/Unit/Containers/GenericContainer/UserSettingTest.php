<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\UserSetting;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

final class UserSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testUserIsProvidedByUserSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(UserSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testUserFromStaticUser()
    {
        $container = (new UserSettingWithStaticUserContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommands(['whoami'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame("nobody\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithUser()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withUser('nobody')
            ->withCommands(['whoami'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame("nobody\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testUserRejectsInvalidStaticConfiguration()
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
