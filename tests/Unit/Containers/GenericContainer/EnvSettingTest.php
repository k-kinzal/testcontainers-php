<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\EnvSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

final class EnvSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testEnvIsProvidedByEnvSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(EnvSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testEnvFromStaticEnvironments()
    {
        $container = (new EnvSettingWithStaticEnvironmentsContainer('alpine:latest'))
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'printenv ENV1 ENV2; sleep 300'])
            ->withWaitStrategy((new LogMessageWaitStrategy())->withPattern('^value2$'))
        ;
        $instance = $container->start();

        self::assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithEnv()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnv('ENV1', 'value1')
            ->withEnv('ENV2', 'value2')
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'printenv ENV1 ENV2; sleep 300'])
            ->withWaitStrategy((new LogMessageWaitStrategy())->withPattern('^value2$'))
        ;
        $instance = $container->start();

        self::assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithEnvs()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnvs([
                'ENV1' => 'value1',
                'ENV2' => 'value2',
            ])
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'printenv ENV1 ENV2; sleep 300'])
            ->withWaitStrategy((new LogMessageWaitStrategy())->withPattern('^value2$'))
        ;
        $instance = $container->start();

        self::assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    /**
     * @dataProvider providerWithEnvRejectsInvalidInputs
     * @param string $key
     * @param bool   $value
     * @return void
     */
    public function testWithEnvRejectsInvalidInputs($key, $value)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withEnv($key, $value);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function providerWithEnvRejectsInvalidInputs()
    {
        return [
            'case 1' => ['KEY', false],
        ];
    }

    /**
     * @dataProvider providerWithEnvsRejectsInvalidInputs
     * @param array{'KEY': stdClass} $env
     * @return void
     */
    public function testWithEnvsRejectsInvalidInputs($env)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withEnvs($env);
    }

    /**
     * @return array<string, array{array{'KEY': stdClass}}>
     */
    public static function providerWithEnvsRejectsInvalidInputs()
    {
        return [
            'case 1' => [['KEY' => new stdClass()]],
        ];
    }

    /**
     * @return void
     */
    public function testEnvRejectsInvalidStaticConfiguration()
    {
        $container = new EnvSettingWithInvalidEnvContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class EnvSettingWithStaticEnvironmentsContainer extends GenericContainer
{
    protected static $ENVIRONMENTS = [
        'ENV1' => 'value1',
        'ENV2' => 'value2',
    ];
}

final class EnvSettingWithInvalidEnvContainer extends GenericContainer
{
    protected static $ENV = ['KEY' => false];
}
