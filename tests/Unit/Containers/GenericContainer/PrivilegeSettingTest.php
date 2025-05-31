<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PrivilegeSetting;

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
}

class PrivilegeSettingWithStaticPrivilegedContainer extends GenericContainer
{
    protected static $PRIVILEGED = true;
}
