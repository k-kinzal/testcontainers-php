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
    /**
     * @return void
     */
    public function testHasPrivilegeSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(PrivilegeSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticPrivileged()
    {
        $container = new PrivilegeSettingWithStaticPrivilegedContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(true, $instance->getPrivilegedMode());
    }

    /**
     * @return void
     */
    public function testStartWithPrivilegedMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPrivilegedMode(true)
        ;
        $instance = $container->start();

        $this->assertSame(true, $instance->getPrivilegedMode());
    }

    /**
     * @dataProvider providerWithPrivilegedModeInvalidInputs
     * @param string $mode
     * @return void
     */
    public function testWithPrivilegedModeRejectsInvalidInputs($mode)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPrivilegedMode($mode);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerWithPrivilegedModeInvalidInputs()
    {
        return [
            'case 1' => ['false'],
        ];
    }

    /**
     * @return void
     */
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
