<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainerInstance;

class GenericContainerInstanceTest extends TestCase
{
    public function testGetContainerId()
    {
        $instance = new GenericContainerInstance('8188d93d8a27');

        $this->assertSame('8188d93d8a27', $instance->getContainerId());
    }

    public function testIsRunning()
    {
        $instance = new GenericContainerInstance('8188d93d8a27');

        $this->assertFalse($instance->isRunning());
    }

    public function testStop()
    {
        $instance = new GenericContainerInstance('8188d93d8a27');
        $instance->stop();

        $this->assertFalse($instance->isRunning());
    }
}
