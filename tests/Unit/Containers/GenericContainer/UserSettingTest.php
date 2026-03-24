<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\UserSetting;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
class UserSettingTest extends TestCase
{
    public function testHasUserSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(UserSetting::class, $uses);
    }

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
}

class UserSettingWithStaticUserContainer extends GenericContainer
{
    protected static $USER = 'nobody';
}
