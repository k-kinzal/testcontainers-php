# Environment Variables

testcontainers-php supports configuring various aspects of its behavior through environment variables. This document explains the available environment variables and how to use them.

## Docker Client Environment Variables

These environment variables affect how testcontainers-php interacts with the Docker daemon.

| Environment Variable | Description |
|----------------------|-------------|
| `DOCKER_HOST` | The hostname of the Docker instance. For example, `tcp://remote-docker:2375`. This is the standard Docker environment variable used by the Docker CLI and other Docker tools. |

## Host and Port Forwarding Environment Variables

These environment variables control how testcontainers-php handles host resolution and port forwarding.

| Environment Variable | Description |
|----------------------|-------------|
| `TESTCONTAINERS_HOST_OVERRIDE` | Override the hostname retrieved from the container instance with the specified host regardless of the Docker's host. This is useful when you need to access containers from a different host than where Docker is running. |
| `TESTCONTAINERS_SSH_FEEDFORWARDING` | Enable SSH port forwarding to the remote host that starts the container. The value should be a string in the format `[sshUser@]sshHost[:sshPort]`. This is useful when Docker is running on a remote machine and you need to access the container's ports from your local machine. |
| `TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE` | The remote host to which the SSH port forwarding should be enabled. If not specified, `127.0.0.1` is used. |

## Using Environment Variables

You can set these environment variables in your system or in your test setup code:

```php
// Set environment variables in your test setup
putenv('DOCKER_HOST=tcp://remote-docker:2375');
putenv('TESTCONTAINERS_HOST_OVERRIDE=localhost');
```

## Remote Docker Hosts

When working with a remote Docker host, you may need to configure port forwarding to access the exposed ports of your containers. testcontainers-php provides built-in support for SSH port forwarding:

```php
// Enable SSH port forwarding
putenv('TESTCONTAINERS_SSH_FEEDFORWARDING=user@remote-docker:22');

// Start a container
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts(80);

// The container's mapped ports will be accessible through SSH port forwarding
$mappedPort = $container->getMappedPort(80);
$host = $container->getHost();  // This will be 'localhost' or the value of TESTCONTAINERS_HOST_OVERRIDE
```

This is particularly useful in CI/CD environments or when running Docker on a remote machine.

## Example: Docker in Docker (DinD)

When running tests inside a Docker container that needs to start other Docker containers (Docker in Docker), you may need to configure the Docker host:

```php
// Set the Docker host to the Docker daemon running on the host machine
putenv('DOCKER_HOST=tcp://host.docker.internal:2375');

// Override the host to ensure that container ports are accessible
putenv('TESTCONTAINERS_HOST_OVERRIDE=host.docker.internal');

// Now you can start containers as usual
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts(80);
```

## Example: Remote Docker Host with SSH Port Forwarding

When running tests on a machine that doesn't have Docker installed, but you have access to a remote machine with Docker:

```php
// Set the Docker host to the remote Docker daemon
putenv('DOCKER_HOST=tcp://remote-docker:2375');

// Enable SSH port forwarding to access container ports
putenv('TESTCONTAINERS_SSH_FEEDFORWARDING=user@remote-docker:22');

// Override the host to ensure that container ports are accessible
putenv('TESTCONTAINERS_HOST_OVERRIDE=localhost');

// Now you can start containers and access their ports through SSH port forwarding
$container = (new GenericContainer('nginx:latest'))
    ->withExposedPorts(80);
