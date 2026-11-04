<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\UserSetting;
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
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'whoami; sleep 300'])
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
            ->withUser('nobody')
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'whoami; sleep 300'])
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
