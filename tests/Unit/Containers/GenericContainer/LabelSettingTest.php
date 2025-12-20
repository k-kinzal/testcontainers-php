<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\LabelSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
class LabelSettingTest extends TestCase
{
    public function testHasLabelSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(LabelSetting::class, $uses);
    }

    public function testStaticLabels()
    {
        $container = new LabelSettingWithStaticLabelsContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame('VALUE1', $instance->getLabel('KEY1'));
        $this->assertSame('VALUE2', $instance->getLabel('KEY2'));
    }

    public function testStartWithLabel()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withLabel('KEY1', 'VALUE1')
            ->withLabel('KEY2', 'VALUE2')
            ->withCommands(['echo', 'labels'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame('VALUE1', $instance->getLabel('KEY1'));
        $this->assertSame('VALUE2', $instance->getLabel('KEY2'));
    }

    public function testStartWithLabels()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withLabels(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'])
            ->withCommands(['echo', 'labels'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertSame('VALUE1', $instance->getLabel('KEY1'));
        $this->assertSame('VALUE2', $instance->getLabel('KEY2'));
        $this->assertSame(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'], $instance->getLabels());
    }
}

class LabelSettingWithStaticLabelsContainer extends GenericContainer
{
    protected static $LABELS = [
        'KEY1' => 'VALUE1',
        'KEY2' => 'VALUE2',
    ];
}
