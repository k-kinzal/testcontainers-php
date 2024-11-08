<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer;
use Testcontainers\Containers\GenericContainerInstance;
use Testcontainers\Docker\Exception\NoSuchContainerException;

class GenericContainerInstanceTest extends TestCase
{
    public function testGetContainerId()
    {
        $instance = new GenericContainerInstance('8188d93d8a27');

        $this->assertSame('8188d93d8a27', $instance->getContainerId());
    }

    public function testGetOutput()
    {
        $container = new GenericContainer('nginx:1.27.2');
        $instance = $container->start();

        $this->assertNotEmpty($instance->getOutput());
    }

    public function testGetOutputStopsContainer()
    {
        $instance = new GenericContainerInstance('8188d93d8a27'); // not exist container id
        $instance->stop();

        $this->assertFalse($instance->getOutput());
    }

    public function testGetErrorOutput()
    {
        $container = new GenericContainer('nginx:1.27.2');
        $instance = $container->start();

        $this->assertNotEmpty($instance->getErrorOutput());
    }

    public function testGetErrorOutputStopsContainer()
    {
        $instance = new GenericContainerInstance('8188d93d8a27'); // not exist container id
        $instance->stop();

        $this->assertFalse($instance->getErrorOutput());
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
