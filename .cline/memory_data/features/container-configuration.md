# Container Configuration Options

GenericContainer has a wide range of configurable items:

## General Settings
- **Image**: Docker image to use
- **Name**: Container name
- **Command**: Command to run in the container

## Environment Variables
- **Environment Variables**: Environment variables inside the container

## Port Settings
- **Exposed Ports**: Ports to expose from the container
- **Port Strategies**: How to map container ports to host ports

## Network Settings
- **Network Mode**: Network mode (bridge, host, none, etc.)
- **Network Aliases**: Network aliases for the container

## Volume and Mount Settings
- **File System Binds**: Mount host file system to the container
- **Volumes**: Mount volumes from other containers

## Host Settings
- **Additional Hosts**: Additional host entries in the container's /etc/hosts file

## Label Settings
- **Container Labels**: Labels to apply to the container

## Privilege Settings
- **Privileged Mode**: Whether to run the container in privileged mode

## Pull Policy Settings
- **Image Pull Policy**: When to pull the image (always, if not present, never)

## SSH Port Forward Settings
- **SSH Port Forwarding**: Forward container ports to the local machine via SSH

## Startup Settings
- **Startup Timeout**: How long to wait for the container to start
- **Startup Check Strategies**: How to verify the container has started correctly

## Wait Settings
- **Wait Strategies**: How to wait for services inside the container to become available

## Working Directory Settings
- **Working Directory**: Working directory inside the container
