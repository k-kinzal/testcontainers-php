<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\LabelSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

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

    /**
     * @dataProvider providerWithLabelInvalidInputs
     * @param array{} $key
     * @param string  $value
     */
    public function testWithLabelRejectsInvalidInputs($key, $value)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withLabel($key, $value);
    }

    /**
     * @return array<string, array{array{}, string}>
     */
    public static function providerWithLabelInvalidInputs()
    {
        return [
            'case 1' => [[], 'value'],
        ];
    }

    /**
     * @dataProvider providerWithLabelsInvalidInputs
     * @param string $labels
     */
    public function testWithLabelsRejectsInvalidInputs($labels)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withLabels($labels);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerWithLabelsInvalidInputs()
    {
        return [
            'case 1' => ['not an array'],
        ];
    }

    public function testStartRejectsInvalidLabels()
    {
        $container = new LabelSettingWithInvalidLabelsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class LabelSettingWithStaticLabelsContainer extends GenericContainer
{
    protected static $LABELS = [
        'KEY1' => 'VALUE1',
        'KEY2' => 'VALUE2',
    ];
}

class LabelSettingWithInvalidLabelsContainer extends GenericContainer
{
    protected static $LABELS = [1 => 'value'];
}
