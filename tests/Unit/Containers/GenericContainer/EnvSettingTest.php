<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\EnvSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class EnvSettingTest extends TestCase
{
    public function testHasEnvSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(EnvSetting::class, $uses);
    }

    public function testStaticEnvironments()
    {
        $container = (new EnvSettingWithStaticEnvironmentsContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['printenv', 'ENV1', 'ENV2'])
        ;
        $instance = $container->start();

        $this->assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    public function testStartWithEnv()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withEnv('ENV1', 'value1')
            ->withEnv('ENV2', 'value2')
            ->withCommands(['printenv', 'ENV1', 'ENV2'])
        ;
        $instance = $container->start();

        $this->assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    public function testStartWithEnvs()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withEnvs([
                'ENV1' => 'value1',
                'ENV2' => 'value2',
            ])
            ->withCommands(['printenv', 'ENV1', 'ENV2'])
        ;
        $instance = $container->start();

        $this->assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    /**
     * @dataProvider provideWithEnvInvalidInputs
     * @param mixed $key
     * @param mixed $value
     */
    public function testWithEnvRejectsInvalidInputs($key, $value)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withEnv($key, $value);
    }

    /**
     * @return array
     */
    public static function provideWithEnvInvalidInputs()
    {
        return [
            'case 1' => ['KEY', false],
        ];
    }

    /**
     * @dataProvider provideWithEnvsInvalidInputs
     * @param mixed $env
     */
    public function testWithEnvsRejectsInvalidInputs($env)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withEnvs($env);
    }

    /**
     * @return array
     */
    public static function provideWithEnvsInvalidInputs()
    {
        return [
            'case 1' => [['KEY' => new stdClass()]],
        ];
    }

    public function testStartRejectsInvalidEnv()
    {
        $container = new EnvSettingWithInvalidEnvContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class EnvSettingWithStaticEnvironmentsContainer extends GenericContainer
{
    protected static $ENVIRONMENTS = [
        'ENV1' => 'value1',
        'ENV2' => 'value2',
    ];

    protected static $STARTUP_CHECK_STRATEGY = 'one_shot';
}

class EnvSettingWithInvalidEnvContainer extends GenericContainer
{
    protected static $ENV = ['KEY' => false];
}
