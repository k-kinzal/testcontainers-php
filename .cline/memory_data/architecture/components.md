# Architecture of testcontainers-php

## Main Components

The main components of testcontainers-php are:

1. **Testcontainers**: The main class that manages container startup and shutdown.
2. **Container**: Interface representing a container definition.
3. **ContainerInstance**: Interface representing a running container instance.
4. **GenericContainer**: Generic implementation of the Container interface.
5. **DockerClient**: Client for executing Docker commands.
6. **Strategy Classes**: Classes providing functionality for port allocation, startup checking, waiting, etc.

## Class Structure

The class structure of testcontainers-php is separated by functionality into namespaces:

- **Containers**: Container-related classes and interfaces
  - **GenericContainer**: Generic container implementation and traits
  - **PortStrategy**: Port allocation strategies
  - **StartupCheckStrategy**: Startup check strategies
  - **WaitStrategy**: Wait strategies
  - **Types**: Type-safe value objects
- **Docker**: Docker command execution related classes
  - **Command**: Docker commands
  - **Output**: Command outputs
  - **Exception**: Docker-related exceptions
  - **Types**: Docker-related types
- **SSH**: SSH tunneling related classes
- **Hook**: Container lifecycle hooks
- **Exceptions**: General exceptions

## Dependencies

testcontainers-php is implemented with minimal dependencies:

- **symfony/process**: Used for executing Docker commands
- **psr/log**: Used for logging

Additionally, the Docker command-line tool (Docker CLI) is required.
