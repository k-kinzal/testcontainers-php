# testcontainers-php

[![GitHub Actions](https://github.com/k-kinzal/testcontainers-php/actions/workflows/ci.yaml/badge.svg)](https://github.com/k-kinzal/testcontainers-php/actions)
[![CircleCI](https://circleci.com/gh/k-kinzal/testcontainers-php.svg?style=shield)](https://circleci.com/gh/k-kinzal/testcontainers-php)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A PHP implementation of [Testcontainers](https://testcontainers.com/) that supports PHP versions from 5.6 to 8.5, including EOL versions. This library enables you to use Docker containers for your integration tests with minimal dependencies.

If you are using a supported PHP version, consider using the official [testcontainers/testcontainers](https://packagist.org/packages/testcontainers/testcontainers) instead.

## Features

- Supports PHP 5.6 to 8.5 (including EOL versions)
- Minimal dependencies for easy integration
- Complete container lifecycle management
- Various configuration options for containers
- Multiple wait strategies to ensure services are ready
- Support for remote Docker hosts and SSH port forwarding

## Requirements

- Docker command line tool (docker CLI)
- PHP 5.6 or later

## Installation

```bash
composer require --dev k-kinzal/testcontainers-php
```

## Quick Start

```php
<?php

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Testcontainers;

class MyContainer extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';
}

class MyTest extends TestCase
{
    public function test(): void
    {
        // Start the container and get the container instance
        $instance = Testcontainers::run(MyContainer::class);
        
        // Your test code here
        
        // Containers are automatically stopped when the test ends
    }
}
```

## Documentation

For more detailed information, check out the documentation:

- [Getting Started](docs/getting-started.md): A comprehensive guide to installing and using testcontainers-php
- [Container Configuration](docs/container-configuration.md): Learn how to configure containers with various settings
- [Environments](docs/environments.md): Understand how to configure testcontainers-php using environment variables
- [CI Setup](docs/ci-setup.md): Guide for setting up testcontainers-php in CI environments (GitHub Actions, CircleCI)


## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgements

- [Testcontainers](https://testcontainers.com/) for the original concept and implementation in other languages
- All contributors who have helped improve this project
