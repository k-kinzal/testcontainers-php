# Basic Usage

## Basic Usage Example

```php
<?php

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;

class MyContainer extends GenericContainer
{
    protected const IMAGE = 'alpine:latest';
}

class MyTest extends TestCase
{
    public function test()
    {
        $instance = Testcontainers::run(MyContainer::class);
        
        // Test code
    }
}
```

## Configuration Methods

There are three ways to configure GenericContainer:

### Configuration via Static Properties

```php
class MyContainer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';
    protected static $EXPOSED_PORTS = [80, 443];
    protected static $ENVIRONMENTS = [
        'NGINX_HOST' => 'example.com',
        'NGINX_PORT' => '80'
    ];
}
```

### Configuration via Method Overrides

```php
class MyContainer extends GenericContainer
{
    protected function image()
    {
        return 'nginx:latest';
    }
    
    protected function exposedPorts()
    {
        return [80, 443];
    }
    
    protected function env()
    {
        return [
            'NGINX_HOST' => 'example.com',
            'NGINX_PORT' => '80'
        ];
    }
}
```

### Configuration via Fluent API

```php
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts([80, 443])
    ->withEnv('NGINX_HOST', 'example.com')
    ->withEnv('NGINX_PORT', '80');
```

## Configuration via Environment Variables

testcontainers-php can also be configured using environment variables. Main environment variables include:

- `TESTCONTAINERS_DOCKER_CLIENT_*`: Docker Client settings (command, global options, working directory, environment variables, timeout, etc.)
- `TESTCONTAINERS_HOST_*`: Host-related settings (hostname, port, etc.)
- `TESTCONTAINERS_SSH_FEEDFORWARDING`: SSH port forwarding settings

## Docker Client Configuration

Docker Client configuration can be done in the following ways:

- **DockerClientFactory::config**: Configures the global Docker Client.
- **withDockerClient**: Configures the Docker Client for a specific container.
