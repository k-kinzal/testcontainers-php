# testcontainers-php

This is a PHP implementation of Testcontainers. It supports PHP versions that have reached end-of-life (EOL) to assist legacy PHP projects and can be integrated into any project due to its minimal dependencies.

If you are using a supported PHP version, consider using [testcontainers/testcontainers](https://packagist.org/packages/testcontainers/testcontainers) instead.

## Requirements

- Docker command line tool

## Getting Started

```bash
$ composer require k-kinzal/testcontainers-php
```

## Usage

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
        Testcontainers::start(MyContainer::class);
        
        // Your test code
    }
}
```