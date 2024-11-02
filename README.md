# testcontainers-php

**NOTE: This project is experimental and not ready for production use.**

This is a PHP implementation of Testcontainers. It supports PHP versions that have reached end-of-life (EOL) to assist legacy PHP projects and can be integrated into any project due to its minimal dependencies.

If you are using a supported PHP version, consider using [testcontainers/testcontainers](https://packagist.org/packages/testcontainers/testcontainers) instead.

## Requirements

- Docker command line tool

## Getting Started

Add the repository to your `composer.json`:

```json
"repositories": {
  "k-kinzal/testcontainers-php": {
    "type": "vcs",
    "url": "https://github.com/k-kinzal/testcontainers-php"
  }
}
```

```bash
$ composer require k-kinzal/testcontainers-php
```

**NOTE: This library will not be available on Packagist until it reaches version 1.0.**

## Usage

```php
<?php

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function tearDown()
    {
        Testcontainers::stop();
    }

    public function test()
    {
        Testcontainers::start(YourContainer::class);
        
        // Your test code
    }
}
```