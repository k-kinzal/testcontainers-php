<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PrivilegeSetting;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class PrivilegeSettingTest extends TestCase
{
    public function testHasPrivilegeSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(PrivilegeSetting::class, $uses);
    }

    public function testStaticPrivileged()
    {
        $container = new PrivilegeSettingWithStaticPrivilegedContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(true, $instance->getPrivilegedMode());
    }

    public function testStartWithPrivilegedMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPrivilegedMode(true)
        ;
        $instance = $container->start();

        $this->assertSame(true, $instance->getPrivilegedMode());
    }

    /**
     * @dataProvider provideWithPrivilegedModeInvalidInputs
     * @param mixed $mode
     */
    public function testWithPrivilegedModeRejectsInvalidInputs($mode)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPrivilegedMode($mode);
    }

    /**
     * @return array
     */
    public static function provideWithPrivilegedModeInvalidInputs()
    {
        return [
            'case 1' => ['false'],
        ];
    }

    public function testStartRejectsInvalidPrivileged()
    {
        $container = new PrivilegeSettingWithInvalidPrivilegedContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class PrivilegeSettingWithStaticPrivilegedContainer extends GenericContainer
{
    protected static $PRIVILEGED = true;
}

class PrivilegeSettingWithInvalidPrivilegedContainer extends GenericContainer
{
    protected static $PRIVILEGED = 'false';
}
