<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GeneralSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
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
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommand('pwd')
        ;
        $instance = $container->start();

        $this->assertSame("/\n", $instance->getOutput());
    }

    public function testStartWithCommands()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['echo', 'Hello, World!'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    /**
     * @dataProvider provideWithCommandsInvalidInputs
     * @param mixed $commandParts
     */
    public function testWithCommandsRejectsInvalidInputs($commandParts)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withCommands($commandParts);
    }

    /**
     * @return array
     */
    public static function provideWithCommandsInvalidInputs()
    {
        return [
            'case 1' => [['echo', new stdClass()]],
        ];
    }

    public function testStartRejectsInvalidName()
    {
        $container = new GeneralSettingWithInvalidNameContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    public function testStartRejectsInvalidCommands()
    {
        $container = new GeneralSettingWithInvalidCommandsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class GeneralSettingWithStaticCommandsContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    protected static $COMMANDS = ['echo', 'Hello, World!'];

    protected static $STARTUP_CHECK_STRATEGY = 'one_shot';
}

class GeneralSettingWithInvalidNameContainer extends GenericContainer
{
    protected static $NAME = [];
}

class GeneralSettingWithInvalidCommandsContainer extends GenericContainer
{
    protected static $COMMANDS = ['echo', false];
}
