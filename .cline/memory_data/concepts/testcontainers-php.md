# testcontainers-php

## Position of testcontainers-php

testcontainers-php is a library for easily managing Docker containers during PHP test execution. Testcontainers supports the most common languages and platforms including Java, .NET, Go, NodeJS, Python, and Rust, and testcontainers-php provides similar functionality as a PHP implementation of these Testcontainers libraries.

In particular, this library supports a wide range of PHP versions (5.6 to 8.3), including PHP versions that have reached EOL (End of Life). If you are using supported PHP versions, it is recommended to use the official [testcontainers/testcontainers](https://packagist.org/packages/testcontainers/testcontainers).

## Main Uses and Purposes

The main purpose of testcontainers-php is to improve the reproducibility and reliability of tests by using Docker containers in the test environment. Specifically:

- Ensuring consistency in the test environment, eliminating the "it works on my machine" problem
- Providing external dependencies such as databases, cache servers, and message queues as isolated containers
- Enabling modern testing techniques even for legacy PHP projects
- Easy integration with various projects due to minimal dependencies

## Container Lifecycle

testcontainers-php manages the entire lifecycle of containers:

1. **Container Definition**: Extend the GenericContainer class to define a container
2. **Container Configuration**: Configure using methods like withExposedPorts() and withEnv()
3. **Container Startup**: When Testcontainers::run() is called, the container is created and started, beforeStart() hooks, startup check strategies, wait strategies, and afterStart() hooks are executed, and a ContainerInstance object is returned
4. **Container Usage**: Use the container instance during testing
5. **Container Shutdown**: Containers are automatically stopped in the following ways:
   - When Testcontainers::stop() is explicitly called
   - When the PHP script ends (via a shutdown handler)
   - When the ContainerInstance object is destroyed (via the destructor)

Normally, you don't need to explicitly call `Testcontainers::stop()`, but it can be used when you want to stop a container at a specific time before the test ends, when you want to test behavior after a container is stopped, or when you are running multiple containers and want to stop only some of them.

## Legacy PHP Support

This library supports a wide range of PHP versions (5.6 to 8.3), including PHP versions that have reached EOL (End of Life). This allows modern testing techniques to be used even in legacy PHP projects.

If you are using supported PHP versions, it is recommended to use the official [testcontainers/testcontainers](https://packagist.org/packages/testcontainers/testcontainers).

## Minimal Dependencies

testcontainers-php is implemented with minimal dependencies:

- **symfony/process**: Used for executing Docker commands
- **psr/log**: Used for logging

This allows easy integration with various projects.

Additionally, the Docker command-line tool (Docker CLI) is required.
