<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\WorkdirSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
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
            ->withAutoRemoveOnExit(false)
            ->withCommands(['pwd'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("/tmp\n", $instance->getOutput());
    }

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
     * @dataProvider provideWithWorkingDirectoryInvalidInputs
     * @param mixed $workDir
     */
    public function testWithWorkingDirectoryRejectsInvalidInputs($workDir)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withWorkingDirectory($workDir);
    }

    /**
     * @return array
     */
    public static function provideWithWorkingDirectoryInvalidInputs()
    {
        return [
            'case 1' => [[]],
        ];
    }

    public function testStartRejectsInvalidWorkdir()
    {
        $container = new WorkdirSettingWithInvalidWorkdirContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class WorkdirSettingWithStaticWorkdirContainer extends GenericContainer
{
    protected static $WORKDIR = '/tmp';
}

class WorkdirSettingWithInvalidWorkdirContainer extends GenericContainer
{
    protected static $WORKDIR = false;
}
