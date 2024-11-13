<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer;
use Testcontainers\Containers\GenericContainerInstance;
use Testcontainers\Containers\ImagePullPolicy;
use Testcontainers\Docker\Exception\NoSuchContainerException;

class GenericContainerInstanceTest extends TestCase
{
    public function testGetContainerId()
    {
        $instance = new GenericContainerInstance('8188d93d8a27');

        $this->assertSame('8188d93d8a27', $instance->getContainerId());
    }

    public function testGetLabel()
    {
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'labels' => [
                'com.example.label' => 'value',
            ],
        ]);

        $this->assertSame('value', $instance->getLabel('com.example.label'));
    }

    public function testGetLabels()
    {
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'labels' => [
                'com.example.label' => 'value',
            ],
        ]);

        $this->assertSame(['com.example.label' => 'value'], $instance->getLabels());
    }

    public function testGetHost()
    {
        $instance = new GenericContainerInstance('8188d93d8a27');

        $this->assertSame('localhost', $instance->getHost());
    }

    public function testGetMappedPort()
    {
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'ports' => [
                80 => 8080,
            ],
        ]);

        $this->assertSame(8080, $instance->getMappedPort(80));
    }

    public function testGetImagePullPolicy()
    {
        $pullPolicy = ImagePullPolicy::ALWAYS();
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'pull' => $pullPolicy,
        ]);

        $this->assertSame($pullPolicy, $instance->getImagePullPolicy());
    }

    public function testGetPrivilegedMode()
    {
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'privileged' => true,
        ]);

        $this->assertTrue($instance->getPrivilegedMode());
    }

    public function testGetOutput()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['echo', 'Hello, World!']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testGetErrorOutput()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['ls', '/not-exist-dir']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("ls: /not-exist-dir: No such file or directory\n", $instance->getErrorOutput());
    }

    public function testSetDataAndGetData()
    {
        $data = new CustomData();

        $instance = new GenericContainerInstance('8188d93d8a27');
        $instance->setData($data);

        $this->assertSame($data, $instance->getData(CustomData::class));
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

class CustomData
{
}
