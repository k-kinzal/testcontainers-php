# Container Configuration

This guide explains how to configure containers in testcontainers-php.

## Configuration Methods

There are three ways to configure a container in testcontainers-php:

1. **Static Properties**: Define static properties in your container class when extending `GenericContainer`.
2. **Method Overrides**: Override protected getter methods in your container class.
3. **Fluent API**: Use the fluent API methods (`withXXX()`) to configure a container instance.

### 1. Static Properties

When extending the `GenericContainer` class, you can define static properties to configure the container:

```php
class MyContainer extends GenericContainer
{
    // Required: Docker image to use
    protected static $IMAGE = 'nginx:latest';
    
    // Optional: Container name
    protected static $NAME = 'my-nginx-container';
    
    // Optional: Command to run in the container
    protected static $COMMANDS = [
        'nginx',
        '-g',
        'daemon off;'
    ];
    
    // Optional: Environment variables
    protected static $ENVIRONMENTS = [
        'NGINX_HOST' => 'example.com',
        'NGINX_PORT' => '80'
    ];
    
    // Optional: Ports to expose
    protected static $EXPOSED_PORTS = [80, 443];
    
    // Optional: Port strategy
    protected static $PORT_STRATEGY = 'random';
}
```

### 2. Method Overrides

You can override protected getter methods in your container class to configure the container:

```php
class MyContainer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';
    
    // Override the name method
    protected function name()
    {
        return 'my-nginx-container';
    }
    
    // Override the commands method
    protected function commands()
    {
        return ['nginx', '-g', 'daemon off;'];
    }
    
    // Override the env method
    protected function env()
    {
        return [
            'NGINX_HOST' => 'example.com',
            'NGINX_PORT' => '80'
        ];
    }
    
    // Override the exposedPorts method
    protected function exposedPorts()
    {
        return [80, 443];
    }
    
    // Override the portStrategy method
    protected function portStrategy()
    {
        return new RandomPortStrategy();
    }
}
```

### 3. Fluent API

You can use the fluent API methods to configure a container instance:

```php
$container = (new GenericContainer('nginx:latest'))
    ->withName('my-nginx-container')
    ->withCommands(['nginx', '-g', 'daemon off;'])
    ->withEnv('NGINX_HOST', 'example.com')
    ->withEnv('NGINX_PORT', '80')
    ->withExposedPorts([80, 443])
    ->withPortStrategy(new RandomPortStrategy());
```

## Available Configuration Options

The following configuration options are available for containers:

### General Settings

General settings define the basic properties of a container.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$IMAGE` | `image()` | Docker image to use. This is the only required setting and specifies which Docker image will be pulled and used for the container. Example: `nginx:latest`, `mysql:8.0`. |
| `$NAME` | `name()` | Container name. This allows you to assign a custom name to the container for easier identification in Docker commands and logs. If not specified, Docker will generate a random name. |
| `$COMMANDS` | `commands()` | Command to run in the container. This overrides the default command specified in the Docker image. Useful when you need to customize the container's startup behavior or pass specific arguments to the entrypoint. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';
    protected static $NAME = 'my-nginx-container';
    protected static $COMMANDS = ['nginx', '-g', 'daemon off;'];
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function image()
    {
        return 'nginx:latest';
    }
    
    protected function name()
    {
        return 'my-nginx-container';
    }
    
    protected function commands()
    {
        return ['nginx', '-g', 'daemon off;'];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withName('my-nginx-container')
    ->withCommands(['nginx', '-g', 'daemon off;']);
```

### Environment Variables

Environment variables allow you to pass configuration to the container at runtime.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$ENVIRONMENTS`, `$ENV` | `env()` | Environment variables to set in the container. These are key-value pairs that will be available to processes running inside the container. Commonly used to configure applications without modifying their code, such as setting database credentials, API keys, or application modes (development, production, etc.). |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $ENVIRONMENTS = [
        'NGINX_HOST' => 'example.com',
        'NGINX_PORT' => '80'
    ];
    
    // Alternatively:
    // protected static $ENV = [
    //     'NGINX_HOST' => 'example.com',
    //     'NGINX_PORT' => '80'
    // ];
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function env()
    {
        return [
            'NGINX_HOST' => 'example.com',
            'NGINX_PORT' => '80'
        ];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withEnv('NGINX_HOST', 'example.com')
    ->withEnv('NGINX_PORT', '80');
    
// Or using withEnvs:
// $container = (new GenericContainer('nginx:latest'))
//     ->withEnvs([
//         'NGINX_HOST' => 'example.com',
//         'NGINX_PORT' => '80'
//     ]);
```

### Port Settings

Port settings control how container ports are exposed and mapped to host ports.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$EXPOSED_PORTS`, `$EXPOSE`, `$PORTS` | `exposedPorts()` | Ports to expose from the container to the host. These are the ports that your container's services are listening on internally. When you expose a port, testcontainers-php will map it to a port on the host machine, allowing your tests to connect to the containerized service. |
| `$PORT_STRATEGY` | `portStrategy()` | Strategy for allocating host ports. This determines how host ports are selected when mapping to container ports. Using a strategy like RandomPortStrategy helps avoid port conflicts when running multiple tests in parallel or when specific ports are already in use on the host. |

#### Available Port Strategies

| Strategy | Description | Use Case |
|----------|-------------|----------|
| `RandomPortStrategy` | Selects a random port from the ephemeral port range (49152-65535) | When you need a dynamic port allocation to avoid conflicts with other services |

The `RandomPortStrategy` has a conflict behavior of `RETRY`, which means if a port conflict occurs, it will automatically try another port.

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 443];
    
    // Alternatively:
    // protected static $EXPOSE = [80, 443];
    // protected static $PORTS = [80, 443];
    
    protected static $PORT_STRATEGY = 'random';
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function exposedPorts()
    {
        return [80, 443];
    }
    
    protected function portStrategy()
    {
        return new RandomPortStrategy();
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts([80, 443])
    ->withPortStrategy(new RandomPortStrategy());
```

### Network Settings

Network settings control how the container connects to networks and how it can be discovered by other containers.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$NETWORK_MODE` | `networkMode()` | Network mode for the container. This determines how the container connects to Docker networks. Common values include: 'bridge' (default Docker network), 'host' (shares the host's network stack), 'none' (no networking), or a custom network name. Using the right network mode is important for container-to-container communication. |
| `$NETWORK_ALIASES` | `networkAliases()` | Network aliases for the container. These are alternative names that can be used to reach the container from other containers in the same network. Useful when you want to reference a container by a specific name in your application configuration, regardless of the actual container name. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $NETWORK_MODE = 'bridge';
    protected static $NETWORK_ALIASES = ['web', 'nginx'];
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function networkMode()
    {
        return 'bridge';
    }
    
    protected function networkAliases()
    {
        return ['web', 'nginx'];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withNetworkMode('bridge')
    ->withNetworkAlias('web')
    ->withNetworkAlias('nginx');

// Alternatively, you can set multiple aliases at once:
// $container = (new GenericContainer('nginx:latest'))
//     ->withNetworkAliases(['web', 'nginx']);
```

### Volume and Mount Settings

Volume and mount settings allow you to share files and directories between the host and the container.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$MOUNTS` | `mounts()` | Bind mounts that map host directories or files to container paths. This allows the container to access files from the host system, which is useful for sharing test fixtures, configuration files, or application code with the container. Bind mounts can be read-only or read-write. |
| - | `volumesFrom()` | Volumes from other containers. This allows the container to access volumes that are mounted in another container. Useful when you need to share data between containers, such as when one container generates data that another container needs to process. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $MOUNTS = [
        '/path/on/host:/path/in/container:ro'
    ];
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function mounts()
    {
        return ['/path/on/host:/path/in/container:ro'];
    }
    
    protected function volumesFrom()
    {
        return ['other-container-name'];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withFileSystemBind(
        '/path/on/host',
        '/path/in/container',
        \Testcontainers\Containers\BindMode::READ_ONLY()
    )
    ->withVolumesFrom('other-container-name');

// Alternatively, you can use these aliases:
// $container = (new GenericContainer('nginx:latest'))
//     ->withVolume(
//         '/path/on/host',
//         '/path/in/container',
//         \Testcontainers\Containers\BindMode::READ_ONLY()
//     );
// 
// $container = (new GenericContainer('nginx:latest'))
//     ->withMount(
//         '/path/on/host',
//         '/path/in/container',
//         \Testcontainers\Containers\BindMode::READ_ONLY()
//     );
// 
// // For multiple mounts:
// $container = (new GenericContainer('nginx:latest'))
//     ->withFileSystemBinds([
//         '/path/on/host1:/path/in/container1:ro',
//         '/path/on/host2:/path/in/container2:rw'
//     ]);
```

### Host Settings

Host settings allow you to configure how the container interacts with the host system and other hosts.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$EXTRA_HOSTS` | `extraHosts()` | Extra host entries to add to the container's `/etc/hosts` file. This allows the container to resolve specific hostnames to IP addresses that you define. Commonly used to make the container aware of services running on the host machine or to override DNS resolution for specific domains. The special value `host-gateway` can be used to reference the host machine from within the container. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $EXTRA_HOSTS = [
        'host.docker.internal:host-gateway',
        'example.com:192.168.1.1'
    ];
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function extraHosts()
    {
        return [
            'host.docker.internal:host-gateway',
            'example.com:192.168.1.1'
        ];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withExtraHost('host.docker.internal', 'host-gateway')
    ->withExtraHost('example.com', '192.168.1.1');
```

### Label Settings

Label settings allow you to add metadata to your containers.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$LABELS` | `labels()` | Container labels are key-value pairs that add metadata to your containers. These labels are not visible to processes running inside the container but are stored with the container configuration. They can be used for organization, automation, or to provide information about the container's purpose. Common uses include version tracking, environment identification, or grouping containers by application. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $LABELS = [
        'com.example.environment' => 'test',
        'com.example.version' => '1.0'
    ];
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function labels()
    {
        return [
            'com.example.environment' => 'test',
            'com.example.version' => '1.0'
        ];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withLabel('com.example.environment', 'test')
    ->withLabel('com.example.version', '1.0');
```

### Privilege Settings

Privilege settings control the security context of the container.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$PRIVILEGED` | `privileged()` | Privileged mode gives the container nearly all the same access to the host as processes running outside containers on the host. This is generally not recommended for security reasons, but may be necessary for containers that need to access host devices or perform system-level operations. Use with caution, as privileged containers can potentially modify the host system. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $PRIVILEGED = true;
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function privileged()
    {
        return true;
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withPrivilegedMode(true);
```

### Pull Policy Settings

Pull policy settings control when Docker should pull images from a registry.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$PULL_POLICY` | `pullPolicy()` | Image pull policy determines when Docker should pull the image from a registry. Available options are: `ALWAYS` (always pull the image, even if it exists locally), `IF_NOT_PRESENT` (only pull if the image doesn't exist locally), and `NEVER` (never pull the image, fail if it doesn't exist locally). This setting is useful for controlling network usage and ensuring you're using the expected image version. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $PULL_POLICY = 'always';
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function pullPolicy()
    {
        return \Testcontainers\Containers\Types\ImagePullPolicy::ALWAYS();
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withImagePullPolicy(\Testcontainers\Containers\Types\ImagePullPolicy::ALWAYS());
```

### Startup Settings

Startup settings control how the container's startup process is handled.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$STARTUP_TIMEOUT` | `startupTimeout()` | Startup timeout in seconds. This is the maximum time to wait for the container to start before considering it failed. If the container doesn't start within this time, an exception will be thrown. Default is typically 60 seconds, but you may need to increase this for containers with longer startup times, such as databases or complex applications. |
| `$STARTUP_CHECK_STRATEGY` | `startupCheckStrategy()` | Strategy used to determine if a container has started successfully. This defines the criteria for considering a container "started" and ready for use. Different strategies can check for different conditions, such as the container being in the "running" state or having exited with a specific exit code. |

#### Available Startup Check Strategies

| Strategy | Description | Use Case |
|----------|-------------|----------|
| `IsRunningStartupCheckStrategy` | Waits until the container is in the "running" state or has exited with a zero exit code | When you need to ensure the container has started successfully before proceeding |

The `IsRunningStartupCheckStrategy` continuously checks the container's state and returns:
- `true` if the container is in the "running" state
- `true` if the container has exited with a zero exit code (indicating successful completion)
- `false` otherwise

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $STARTUP_TIMEOUT = 60; // seconds
    protected static $STARTUP_CHECK_STRATEGY = 'is_running';
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function startupTimeout()
    {
        return 60; // seconds
    }
    
    protected function startupCheckStrategy()
    {
        return new \Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy();
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withStartupTimeout(60)
    ->withStartupCheckStrategy(
        new \Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy()
    );
```

### Wait Settings

Wait settings control how testcontainers-php waits for the container to be ready for use.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$WAIT_STRATEGY` | `waitStrategy()` | Wait strategy determines how testcontainers-php checks if the container is ready for use. Unlike startup strategies that just check if the container is running, wait strategies verify that the service inside the container is actually ready to accept connections or process requests. This is crucial for ensuring your tests don't start interacting with a container before its services are fully initialized. |

#### Available Wait Strategies

| Strategy | Description | Use Case |
|----------|-------------|----------|
| `HostPortWaitStrategy` | Waits until specified ports on the container are available | When you need to ensure that a service inside the container is listening on specific ports |
| `HttpWaitStrategy` | Waits until a specified HTTP endpoint is reachable | When you need to ensure that a web server or API inside the container is ready to accept requests |
| `LogMessageWaitStrategy` | Waits until a specified log message appears in the container logs | When you need to wait for a specific message in the logs that indicates the service is ready |
| `PDOConnectWaitStrategy` | Waits until a PDO connection can be established | When you need to ensure that a database inside the container is ready to accept connections |

Each wait strategy has a default timeout of 30 seconds, which can be customized using the `withTimeoutSeconds()` method.

#### Example: HTTP Wait Strategy

```php
// Static Property
class MyWebServer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';
    protected static $WAIT_STRATEGY = 'http';
}

// Method Override
class MyWebServer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';
    
    protected function waitStrategy()
    {
        return (new \Testcontainers\Containers\WaitStrategy\HttpWaitStrategy())
            ->withPort(80)
            ->withPath('/')
            ->withExpectedResponseCode(200)
            ->withTimeoutSeconds(30);
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts([80])
    ->withWaitStrategy(
        (new \Testcontainers\Containers\WaitStrategy\HttpWaitStrategy())
            ->withPort(80)
            ->withPath('/')
            ->withExpectedResponseCode(200)
            ->withTimeoutSeconds(30)
    );
```

### Workdir Settings

Workdir settings control the working directory inside the container.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$WORKDIR` | `workDir()` | Working directory inside the container. This sets the directory that will be used as the current working directory for any commands that run in the container. It's similar to using the `WORKDIR` directive in a Dockerfile. This is useful when your application expects to run from a specific directory, or when you want to simplify paths in your commands by setting a base directory. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $WORKDIR = '/app';
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function workDir()
    {
        return '/app';
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withWorkingDirectory('/app');

// Alternatively, you can use this alias:
// $container = (new GenericContainer('nginx:latest'))
//     ->withWorkDir('/app');
```

### SSH Port Forward Settings

SSH Port Forward settings control how testcontainers-php sets up SSH port forwarding to the remote host that starts the container.

| Static Property | Method | Description |
|-----------------|--------|-------------|
| `$SSH_PORT_FORWARD` | `sshPortForward()` | SSH port forwarding configuration. This allows you to set up SSH port forwarding to access container ports from your local machine when Docker is running on a remote host. The value can be a string in the format `[user@]host[:port]`, or a boolean. When set to `true`, it will use the default SSH configuration. |

#### Example

```php
// Static Property
class MyContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80];
    protected static $SSH_PORT_FORWARD = 'user@remote-host:22';
}

// Method Override
class MyContainer extends GenericContainer
{
    protected function sshPortForward()
    {
        return [
            'sshUser' => 'user',
            'sshHost' => 'remote-host',
            'sshPort' => 22,
        ];
    }
}

// Fluent API
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts([80])
    ->withSSHPortForward('remote-host', 22, 'user');
```

This setting is particularly useful when you need to access container ports from your local machine, but Docker is running on a remote host. When SSH port forwarding is enabled, testcontainers-php will automatically set up an SSH tunnel for each exposed port, allowing you to access the container's services as if they were running locally.

> **Note**: SSH port forwarding can also be configured using the `TESTCONTAINERS_SSH_FEEDFORWARDING` environment variable. See the [Environments](environments.md) documentation for more information.

## Docker Client Configuration

The Docker client is responsible for executing Docker commands. testcontainers-php provides a way to customize the Docker client used by containers.

### Using a Custom Docker Client

You can provide a custom Docker client to a container using the `withDockerClient` method. This is useful when you need to customize the Docker command execution, such as using a different Docker binary, adding global options, or changing the working directory.

```php
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;

// Create a custom Docker client
$dockerClient = (new DockerClient())
    ->withCommand('/usr/local/bin/docker')  // Use a specific Docker binary
    ->withGlobalOptions(['--tls', '--tlsverify'])  // Add global options
    ->withCwd('/path/to/working/directory')  // Set working directory
    ->withEnv(['DOCKER_HOST' => 'tcp://remote-docker:2375'])  // Set environment variables
    ->withTimeout(120);  // Set timeout in seconds

// Use the custom Docker client with a container
$container = (new GenericContainer('nginx:latest'))
    ->withDockerClient($dockerClient);
```

### Global Docker Client Configuration

You can also configure the Docker client globally using the `DockerClientFactory::config` method. This configuration will be used by all containers that don't have a specific Docker client set.

```php
use Testcontainers\Docker\DockerClientFactory;

// Configure the Docker client globally
DockerClientFactory::config([
    'command' => '/usr/local/bin/docker',  // Use a specific Docker binary
    'globalOptions' => ['--tls', '--tlsverify'],  // Add global options
    'cwd' => '/path/to/working/directory',  // Set working directory
    'env' => ['DOCKER_HOST' => 'tcp://remote-docker:2375'],  // Set environment variables
    'timeout' => 120,  // Set timeout in seconds
]);

// All containers will use the global configuration
$container1 = new GenericContainer('nginx:latest');
$container2 = new GenericContainer('mysql:8.0');
```

> **Note**: For information about environment variables that can be used to configure testcontainers-php, see the [Environments](environments.md) documentation.
