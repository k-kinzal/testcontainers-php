# Getting Started

This guide explains how to install and use testcontainers-php for your testing needs.

## Requirements

- Docker command line tool (docker CLI) must be installed and accessible. See the [Docker installation documentation](https://docs.docker.com/get-docker/) for instructions.

## Installation

Install testcontainers-php using Composer as a development dependency:

```bash
composer require --dev k-kinzal/testcontainers-php
```

## Basic Usage

### 1. Define a Container Class

First, define a container class by extending the `GenericContainer` class and specifying the Docker image you want to use:

```php
<?php

namespace YourNamespace;

use Testcontainers\Containers\GenericContainer\GenericContainer;

class MyContainer extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';
    
    // Optional: Specify the command to run in the container
    protected static $COMMANDS = [
        'tail',
        '-f',
        '/dev/null',
    ];
}
```

### 2. Use the Container in Tests

Next, use the container in your PHPUnit tests:

```php
<?php

namespace YourNamespace\Tests;

use PHPUnit\Framework\TestCase;
use Testcontainers\Testcontainers;
use YourNamespace\MyContainer;

class MyTest extends TestCase
{
    public function testSomething(): void
    {
        // Start the container and get the container instance
        $instance = Testcontainers::run(MyContainer::class);
        
        // Your test code here
    }
}
```



## Container Lifecycle Management

testcontainers-php manages the complete lifecycle of Docker containers for your tests:

1. **Container Definition**: First, you define a container by extending the `GenericContainer` class and specifying the Docker image to use.

2. **Container Configuration**: You can configure the container using various methods like `withExposedPorts()`, `withEnv()`, etc.

3. **Container Startup**: When you call `Testcontainers::run()`, the following happens:
   - The container is created and started
   - If defined, `beforeStart()` hooks are executed before startup
   - If defined, startup check strategies verify the container started correctly
   - If defined, wait strategies ensure services inside the container are ready
   - If defined, `afterStart()` hooks are executed after startup
   - A `ContainerInstance` object is returned for interacting with the running container

4. **Container Usage**: During your test, you can interact with the container through the returned instance.

5. **Container Shutdown**: Containers are automatically stopped in several ways:
   - When `Testcontainers::stop()` is explicitly called
   - When the PHP script ends (via a registered shutdown handler)
   - When the `ContainerInstance` object is destroyed (via the destructor)

You don't need to explicitly call `Testcontainers::stop()` in most cases, as testcontainers-php handles cleanup automatically. However, you might want to stop containers explicitly in scenarios like:

```php
// Stop all containers
Testcontainers::stop();
```

This is useful when:
- You need to free up resources before the test completes
- You want to test behavior after a container is stopped
- You're running multiple containers and need to stop some but not others

## Additional Resources

For more advanced usage, refer to the following resources:

- [Container Configuration](container-configuration.md): Learn how to configure containers with various settings like ports, volumes, environment variables, and more.
- [Environments](environments.md): Understand how to configure testcontainers-php using environment variables and work with different Docker environments.
