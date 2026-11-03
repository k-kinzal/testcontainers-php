<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\WorkdirSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

final class WorkdirSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasWorkdirSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(WorkdirSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticWorkdir()
    {
        $container = (new WorkdirSettingWithStaticWorkdirContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['pwd'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("/tmp\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testStartWithWorkingDirectory()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withWorkingDirectory('/tmp')
            ->withCommands(['pwd'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("/tmp\n", $instance->getOutput());
    }

    /**
     * @dataProvider providerWithWorkingDirectoryInvalidInputs
     * @param array{} $workDir
     * @return void
     */
    public function testWithWorkingDirectoryRejectsInvalidInputs($workDir)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withWorkingDirectory($workDir);
    }

    /**
     * @return array<string, array{array{}}>
     */
    public static function providerWithWorkingDirectoryInvalidInputs()
    {
        return [
            'case 1' => [[]],
        ];
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidWorkdir()
    {
        $container = new WorkdirSettingWithInvalidWorkdirContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class WorkdirSettingWithStaticWorkdirContainer extends GenericContainer
{
    protected static $WORKDIR = '/tmp';
}

final class WorkdirSettingWithInvalidWorkdirContainer extends GenericContainer
{
    protected static $WORKDIR = false;
}
