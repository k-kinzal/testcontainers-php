# Strategy Pattern Implementation

## Port Allocation Strategies

Port allocation strategies define how to map container ports to host machine ports. Currently implemented strategies include:

- **RandomPortStrategy**: Allocates a random port in the range 49152 to 65535. Retries if port conflicts occur.

## Startup Check Strategies

Startup check strategies define how to verify if a container has started correctly. Currently implemented strategies include:

- **IsRunningStartupCheckStrategy**: Checks if the container state is "running", or if it is in "exited" or "dead" state with exit code 0.

## Wait Strategies

Wait strategies define how to wait for services inside the container to become available. Currently implemented strategies include:

- **HostPortWaitStrategy**: Waits for a specified port to become available.
- **HttpWaitStrategy**: Waits for a specified HTTP endpoint to become reachable. Can be configured with methods like withPort, withPath, withExpectedResponseCode, withTimeoutSeconds, etc.
- **LogMessageWaitStrategy**: Waits for a specified log message to appear in the container logs.
- **PDOConnectWaitStrategy**: Waits for a PDO connection to be established. Can be used with database containers like MySQL, SQLite, etc.

## Network Features

testcontainers-php provides the following network features:

- **Port Mapping**: Maps container ports to host machine ports.
- **Network Mode**: Can set network modes like bridge, host, none, etc.
- **Network Aliases**: Can set network aliases for containers.
- **Additional Hosts**: Can set additional host entries in the container's /etc/hosts file.

## Volumes and Mounts

testcontainers-php provides the following volume and mount features:

- **File System Binds**: Mounts the host file system to the container.
- **Volumes**: Mounts volumes from other containers.

## Environment Variables

testcontainers-php provides the following environment variable features:

- **Container Environment Variables**: Sets environment variables inside the container.
- **Configuration via Environment Variables**: testcontainers-php itself can be configured using environment variables.

## SSH Tunneling

testcontainers-php provides SSH tunneling functionality to map container ports to the local machine when running Docker on a remote host.
