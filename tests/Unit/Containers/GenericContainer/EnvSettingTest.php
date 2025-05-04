<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\EnvSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;

/**
 * @internal
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
            ->withCommands(['printenv', 'ENV1', 'ENV2'])
        ;
        $instance = $container->start();

        $this->assertSame("value1\nvalue2\n", $instance->getOutput());
    }

    public function testStartWithEnv()
    {
        $container = (new GenericContainer('alpine:latest'))
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
            ->withEnvs([
                'ENV1' => 'value1',
                'ENV2' => 'value2',
            ])
            ->withCommands(['printenv', 'ENV1', 'ENV2'])
        ;
        $instance = $container->start();

        $this->assertSame("value1\nvalue2\n", $instance->getOutput());
    }
}

class EnvSettingWithStaticEnvironmentsContainer extends GenericContainer
{
    protected static $ENVIRONMENTS = [
        'ENV1' => 'value1',
        'ENV2' => 'value2',
    ];
}
