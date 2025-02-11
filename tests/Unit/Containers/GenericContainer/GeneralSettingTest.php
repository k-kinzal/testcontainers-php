<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GeneralSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

class GeneralSettingTest extends TestCase
{
    public function testHasGeneralSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(GeneralSetting::class, $uses);
    }

    public function testStaticCommands()
    {
        $container = new GeneralSettingWithStaticCommandsContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testStartWithCommand()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommand('pwd');
        $instance = $container->start();

        $this->assertSame("/\n", $instance->getOutput());
    }

    public function testStartWithCommands()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['echo', 'Hello, World!'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }
}

class GeneralSettingWithStaticCommandsContainer extends GenericContainer
{
    protected static $COMMANDS = ['echo', 'Hello, World!'];
}
