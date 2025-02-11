<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\WorkdirSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

class WorkdirSettingTest extends TestCase
{
    public function testHasWorkdirSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(WorkdirSetting::class, $uses);
    }

    public function testStaticWorkdir()
    {
        $container = (new WorkdirSettingWithStaticWorkdirContainer('alpine:latest'))
            ->withCommands(['pwd'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("/tmp\n", $instance->getOutput());
    }

    public function testStartWithWorkingDirectory()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withWorkingDirectory('/tmp')
            ->withCommands(['pwd'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertSame("/tmp\n", $instance->getOutput());
    }
}

class WorkdirSettingWithStaticWorkdirContainer extends GenericContainer
{
    protected static $WORKDIR = '/tmp';
}
