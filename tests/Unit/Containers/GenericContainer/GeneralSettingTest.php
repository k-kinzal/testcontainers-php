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

final class GeneralSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testCommandsIsProvidedByGeneralSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(GeneralSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testCommandsFromStaticCommands()
    {
        $container = new GeneralSettingWithStaticCommandsContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithCommand()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommand('pwd')
        ;
        $instance = $container->start();

        $this->assertSame("/\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithCommands()
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
     * @dataProvider providerWithCommandsRejectsInvalidInputs
     * @param array{string, stdClass} $commandParts
     * @return void
     */
    public function testWithCommandsRejectsInvalidInputs($commandParts)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withCommands($commandParts);
    }

    /**
     * @return array<string, array{array{string, stdClass}}>
     */
    public static function providerWithCommandsRejectsInvalidInputs()
    {
        return [
            'case 1' => [['echo', new stdClass()]],
        ];
    }

    /**
     * @return void
     */
    public function testNameRejectsInvalidStaticConfiguration()
    {
        $container = new GeneralSettingWithInvalidNameContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    /**
     * @return void
     */
    public function testCommandsRejectsInvalidStaticConfiguration()
    {
        $container = new GeneralSettingWithInvalidCommandsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class GeneralSettingWithStaticCommandsContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    protected static $COMMANDS = ['echo', 'Hello, World!'];

    protected static $STARTUP_CHECK_STRATEGY = 'one_shot';
}

final class GeneralSettingWithInvalidNameContainer extends GenericContainer
{
    protected static $NAME = [];
}

final class GeneralSettingWithInvalidCommandsContainer extends GenericContainer
{
    protected static $COMMANDS = ['echo', false];
}
